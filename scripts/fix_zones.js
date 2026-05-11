/**
 * 9jaRide Pro — Add zones to Firestore
 */

const admin = require('firebase-admin');

if (!admin.apps.length) {
  const serviceAccount = require('./firebase-admin-key.json');
  admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),
    databaseURL: 'https://jaride-pro-default-rtdb.firebaseio.com',
  });
}

const db = admin.firestore();

async function fix() {
  console.log('Adding zones to Firestore...\n');

  // Lagos zone
  await db.collection('zone').doc('lagos').set({
    id: 'lagos',
    name: 'Lagos',
    city: 'Lagos',
    state: 'Lagos',
    country: 'Nigeria',
    enable: true,
    position: 1,
    coordinates: {
      latitude: 6.5244,
      longitude: 3.3792,
    },
  });
  console.log('  Added: Lagos');

  // Abuja zone
  await db.collection('zone').doc('abuja').set({
    id: 'abuja',
    name: 'Abuja',
    city: 'Abuja',
    state: 'FCT',
    country: 'Nigeria',
    enable: true,
    position: 2,
    coordinates: {
      latitude: 9.0579,
      longitude: 7.4951,
    },
  });
  console.log('  Added: Abuja');

  // Port Harcourt zone
  await db.collection('zone').doc('port_harcourt').set({
    id: 'port_harcourt',
    name: 'Port Harcourt',
    city: 'Port Harcourt',
    state: 'Rivers',
    country: 'Nigeria',
    enable: true,
    position: 3,
    coordinates: {
      latitude: 4.8156,
      longitude: 7.0498,
    },
  });
  console.log('  Added: Port Harcourt');

  // Update service prices to reference lagos zone
  const services = ['economy', 'premium', 'suv'];
  for (const svc of services) {
    const doc = await db.collection('service').doc(svc).get();
    if (doc.exists) {
      const data = doc.data();
      if (data.prices && data.prices.length > 0) {
        data.prices[0].zoneId = 'lagos';
        await db.collection('service').doc(svc).update({ prices: data.prices });
        console.log(`  Updated ${svc} price zone → lagos`);
      }
    }
  }

  console.log('\n=== Zones added! ===');
  process.exit(0);
}

fix().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
