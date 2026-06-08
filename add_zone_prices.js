const admin = require('./node_modules/firebase-admin');
const sa = require('./storage/app/firebase/credentials.json');
if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(sa) });
}
const db = admin.firestore();

// Base price template — same structure as existing Lagos entry
function priceEntry(zoneId, kmCharge) {
  return {
    zoneId,
    basicFare: '300',
    basicFareCharge: '0',
    kmCharge: String(kmCharge),
    perMinuteCharge: '5',
    holdingMinute: '5',
    holdingMinuteCharge: '10',
    acCharge: '0',
    nonAcCharge: '0',
    isAcNonAc: false,
    nightCharge: '20',
    startNightTime: '22:00',
    endNightTime: '06:00',
  };
}

// Zones to add (their exact IDs from Firestore)
const newZones = [
  { id: '016PrtgJIlnjegukG39G', label: 'Ibadan' },
  { id: 'abuja', label: 'Abuja' },
  { id: 'port_harcourt', label: 'Port Harcourt' },
];

// km charges per service tier
const kmRates = { economy: 50, premium: 80, suv: 100 };

async function run() {
  const serviceIds = ['economy', 'premium', 'suv'];

  for (const svcId of serviceIds) {
    const snap = await db.collection('service').where('id', '==', svcId).get();
    if (snap.empty) { console.log(`WARNING: service "${svcId}" not found`); continue; }

    const doc = snap.docs[0];
    const existing = doc.data().prices || [];
    const existingZoneIds = new Set(existing.map(p => p.zoneId));

    const toAdd = newZones
      .filter(z => !existingZoneIds.has(z.id))
      .map(z => priceEntry(z.id, kmRates[svcId]));

    if (toAdd.length === 0) {
      console.log(`  ${svcId}: all zones already present, skipping`);
      continue;
    }

    await doc.ref.update({ prices: [...existing, ...toAdd] });
    console.log(`  ${svcId}: added zones [${toAdd.map(p => p.zoneId).join(', ')}]`);
  }

  console.log('\nDone. All 4 zones (Lagos, Ibadan, Abuja, Port Harcourt) now in each service.');
  process.exit(0);
}
run().catch(e => { console.error(e); process.exit(1); });
