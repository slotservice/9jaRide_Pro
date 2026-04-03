const functions = require("firebase-functions");
const admin = require("firebase-admin");
const axios = require("axios");

const db = admin.firestore();

// ========== CALCULATE DISTANCE ==========
function distanceKm(lat1, lng1, lat2, lng2) {
  const R = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLng = ((lng2 - lng1) * Math.PI) / 180;
  const a = Math.sin(dLat / 2) ** 2 + Math.cos((lat1 * Math.PI) / 180) * Math.cos((lat2 * Math.PI) / 180) * Math.sin(dLng / 2) ** 2;
  const dist = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  console.log(`distanceKm: (${lat1},${lng1}) -> (${lat2},${lng2}) = ${dist.toFixed(2)} km`);
  return dist;
}

// ========== GET GLOBAL SETTINGS ==========
async function getSettings() {
  const settingsSnap = await db.collection("settings").doc("globalValue").get();
  const data = settingsSnap.data();
  return data;
}

// ========== CALCULATE SURGE ==========
function calculateSurge(demand, supply, surgePercentage) {
  console.log(`Calculating surge: demand=${demand}, supply=${supply}`);
  if (supply <= 0) {
    console.log("No supply, applying surge based on percentage only");
    return 1 + surgePercentage / 100;
  }

  const multiplier = 1 + (surgePercentage / 100) * Math.log2(demand / supply + 1);
  const surge = parseFloat(multiplier.toFixed(2));
  console.log(`Surge calculation: ratio=${(demand / supply).toFixed(2)}, multiplier=${surge}`);
  return surge;
}

// ========== GET ZONE NAME ==========
async function getZoneNameFromLatLng(lat, lng) {
  try {
    console.log(`Fetching zone name for lat=${lat}, lng=${lng}`);
    const res = await axios.get("https://nominatim.openstreetmap.org/reverse", {
      params: { lat, lon: lng, format: "json", zoom: 14, addressdetails: 1 },
      headers: { "User-Agent": "Goride" },
      timeout: 5000,
    });
    const addr = res.data.address || {};
    const area = addr.neighbourhood || addr.suburb || addr.city_district || addr.locality || addr.hamlet || null;
    const main = addr.city || addr.town || addr.village || addr.municipality || null;
    const state = addr.state || null;

    let zoneName;
    if (area && main) zoneName = `${area} - ${main}`;
    else if (main && state) zoneName = `${main} - ${state}`;
    else if (area) zoneName = area;
    else zoneName = "Unknown Area";

    zoneName = zoneName.replace(/\s+/g, " ").trim().toLowerCase().replace(/\b\w/g, (c) => c.toUpperCase());
    console.log(`Zone name resolved: ${zoneName}`);
    return zoneName;
  } catch (err) {
    console.error("Error fetching zone name:", err.message);
    return "Unknown Area";
  }
}

// ========== FIND OR CREATE SURGE ZONE ==========
async function findOrCreateZone(order, settings) {
  console.log(`Finding/creating surge zone for order ID=${order.id}`);
  const orderPoint = new admin.firestore.GeoPoint(order.sourceLocationLAtLng.latitude, order.sourceLocationLAtLng.longitude);

  const zonesSnap = await db.collection("surge_zones").get();
  // Check if order already exists in a zone
  for (const doc of zonesSnap.docs) {
    const z = doc.data();
    if (z.orderIds && z.orderIds.includes(order.id)) {
      console.log(`Order already in zone: ${doc.id}`);
      return doc.id;
    }
  }

  // Match existing zone by radius
  let matchedZone = null;
  for (const doc of zonesSnap.docs) {
    const z = doc.data();
    const dist = distanceKm(orderPoint.latitude, orderPoint.longitude, z.latitude, z.longitude);
    if (dist <= Number(z.radiusKm)) {
      matchedZone = doc;
      break;
    }
  }

  if (matchedZone) {
    console.log(`Adding order to existing zone: ${matchedZone.id}`);
    await matchedZone.ref.update({
      area: admin.firestore.FieldValue.arrayUnion(orderPoint),
      orderIds: admin.firestore.FieldValue.arrayUnion(order.id),
      updatedAt: admin.firestore.FieldValue.serverTimestamp(),
    });
    return matchedZone.id;
  }

  // Create new zone
  const zoneName = await getZoneNameFromLatLng(orderPoint.latitude, orderPoint.longitude);
  const langSnap = await db.collection("languages").where("enable", "==", true).get();
  const activeLanguages = langSnap.docs.map((doc) => doc.data());
  const zoneNameArray = activeLanguages.map((lang) => ({
    name: zoneName,
    type: lang.code,
  }));

  const zoneRef = db.collection("surge_zones").doc();
  await zoneRef.set({
    id: zoneRef.id,
    name: zoneNameArray,
    publish: true,
    area: [orderPoint],
    latitude: orderPoint.latitude,
    longitude: orderPoint.longitude,
    radiusKm: settings.surgeZoneRadius,
    activeRequests: 0,
    onlineDrivers: 0,
    surgeMultiplier: 1.0,
    orderIds: [order.id],
    createdAt: admin.firestore.FieldValue.serverTimestamp(),
    updatedAt: admin.firestore.FieldValue.serverTimestamp(),
  });

  console.log(`Created new surge zone: ${zoneRef.id}`);
  return zoneRef.id;
}

// ========== COUNT ACTIVE ORDERS (DEMAND) ==========
async function countActiveOrders(zone, settings) {
  console.log(`Counting active orders in zone: ${zone.name}`);
  const cutoff = new Date(Date.now() - settings.surgeZoneSelectionMinutes * 60000);
  const snapshot = await db.collection("orders")
    .where("status", "in", ["Ride Placed"])
    .where("createdDate", ">=", cutoff)
    .get();

  let count = 0;
  snapshot.forEach((doc) => {
    const o = doc.data();
    if (!o.sourceLocationLAtLng) return;
    const dist = distanceKm(o.sourceLocationLAtLng.latitude, o.sourceLocationLAtLng.longitude, zone.latitude, zone.longitude);
    if (dist <= zone.radiusKm) count++;
  });

  console.log(`Active orders in last ${settings.surgeZoneSelectionMinutes} minutes: ${count}`);
  return count;
}

// ========== COUNT NEARBY RIDES ==========
async function countNearbyRides(order, settings) {

  const cutoff = new Date(Date.now() - settings.surgeZoneSelectionMinutes * 60000);

  const snapshot = await db.collection("orders")
    .where("status", "in", ["Ride Placed"])
    .where("createdDate", ">=", cutoff)
    .get();

  let count = 0;
  snapshot.forEach((doc) => {
    const o = doc.data();
    if (!o.sourceLocationLAtLng) return;
    const dist = distanceKm(
      order.sourceLocationLAtLng.latitude,
      order.sourceLocationLAtLng.longitude,
      o.sourceLocationLAtLng.latitude,
      o.sourceLocationLAtLng.longitude
    );
    if (dist <= settings.surgeZoneRadius) count++;
  });

  console.log(`NearbyRideCheck order=${order.id} count=${count} threshold=${settings.surgeZoneRideCount}`);
  if (count >= settings.surgeZoneRideCount) {
    console.log(`NearbyRideCheck Threshold MET - zone allowed`);
  }
  return count;
}

// ========== COUNT ONLINE DRIVERS (SUPPLY) ==========
async function countOnlineDrivers(zone) {
  console.log(`Counting online drivers in zone: ${zone.name}`);
  const snapshot = await db.collection("driver_users")
    .where("isOnline", "==", true)
    .where("isEnabled", "==", true)
    .where("documentVerification", "==", true)
    .get();

  let count = 0;
  snapshot.forEach((doc) => {
    const d = doc.data();
    if (!d.location) return;
    const dist = distanceKm(d.location.latitude, d.location.longitude, zone.latitude, zone.longitude);
    if (dist <= zone.radiusKm) count++;
  });

  console.log(`Online drivers count: ${count}`);
  return count;
}

// ========== UPDATE SURGE ZONE ==========
async function updateSurgeZone(zoneId, settings) {
  console.log(`Updating surge zone: ${zoneId}`);
  const zoneRef = db.collection("surge_zones").doc(zoneId);
  const snap = await zoneRef.get();
  if (!snap.exists) return;

  const zone = snap.data();
  const demand = await countActiveOrders(zone, settings);
  const supply = await countOnlineDrivers(zone);

  // Only apply surge if demand > minimum ride count
  let surge = 1.0;
  if (demand >= settings.surgeZoneRideCount) {
    surge = calculateSurge(demand, supply, settings.surgeZoneSurgePercentage);
  }

  // Auto-delete zone if no activity for duration
  const lastUpdated = zone.updatedAt?.toDate ? zone.updatedAt.toDate() : new Date();
  const inactiveMinutes = (Date.now() - lastUpdated.getTime()) / 60000;
  if (inactiveMinutes > settings.surgeZoneDurationMinutes) {
    console.log(`Deleting zone ${zoneId} due to inactivity: ${inactiveMinutes.toFixed(2)} mins`);
    await zoneRef.delete();
    return;
  }

  const cutoff = new Date(Date.now() - settings.surgeZoneSelectionMinutes * 60000);
  const snapshot = await db.collection("orders").where("status", "in", ["Ride Placed"]).where("createdDate", ">=", cutoff).get();
  
  const area = [];
  const activeOrderIds = [];
  snapshot.forEach((doc) => {
    const o = doc.data();
    if (!o.sourceLocationLAtLng) return;
    const dist = distanceKm(o.sourceLocationLAtLng.latitude, o.sourceLocationLAtLng.longitude, zone.latitude, zone.longitude);
    if (dist <= zone.radiusKm) {
      activeOrderIds.push(doc.id);
      area.push(new admin.firestore.GeoPoint(o.sourceLocationLAtLng.latitude, o.sourceLocationLAtLng.longitude));
    }
  });

  await zoneRef.update({
    area,
    orderIds: activeOrderIds,
    activeRequests: demand,
    onlineDrivers: supply,
    surgeMultiplier: surge,
    radiusKm: settings.surgeZoneRadius,
    updatedAt: admin.firestore.FieldValue.serverTimestamp(),
  });

  console.log(`Zone ${zoneId} updated: demand=${demand}, supply=${supply}, surge=${surge}`);
}

// ========== HANDLE COLLECTION CHANGES ==========
async function handleSurgeChange(change, collectionType) {
  const settings = await getSettings();

  if (collectionType === "orders") {
    const order = change.after.exists ? change.after.data() : change.before.data();
    if (!order || !order.sourceLocationLAtLng?.latitude || !order.sourceLocationLAtLng?.longitude) {
      console.log("Order missing required fields, skipping.");
      return;
    }

    // THRESHOLD CHECK BEFORE ZONE CREATION
    const nearbyCount = await countNearbyRides(order, settings);
    if (nearbyCount < settings.surgeZoneRideCount) {
      console.log("Threshold not met — zone not created");
      return;
    }

    console.log(`Processing order: ${order.id}`);
    const zoneId = await findOrCreateZone(order, settings);
    await updateSurgeZone(zoneId, settings);

  } else if (collectionType === "driver_users") {
    console.log("Processing driver updates for all zones");
    const zonesSnap = await db.collection("surge_zones").get();
    const tasks = zonesSnap.docs.map((z) => updateSurgeZone(z.id, settings));
    await Promise.all(tasks);
  }
}

// ========== HANDLE COLLECTION TRIGGER ==========
module.exports.surgeHandler = functions.firestore.document("{collection}/{docId}").onWrite(async (change, context) => {
    const collection = context.params.collection;
    if (collection === "orders") {
      await handleSurgeChange(change, "orders");
    } else if (collection === "driver_users") {
      await handleSurgeChange(change, "driver_users");
    }
});
