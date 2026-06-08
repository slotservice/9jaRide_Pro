const admin = require('./node_modules/firebase-admin');
const sa = require('./storage/app/firebase/credentials.json');

if (!admin.apps.length) {
  admin.initializeApp({
    credential: admin.credential.cert(sa),
    storageBucket: 'jaride-pro.firebasestorage.app',
  });
}

const db = admin.firestore();

const services = [
  {
    id: 'economy',
    names: [
      { languageName: 'en', name: 'Economy' },
      { languageName: 'yo', name: 'Economy' },
      { languageName: 'ha', name: 'Economy' },
      { languageName: 'ig', name: 'Economy' },
      { languageName: 'ar', name: 'Economy' },
      { languageName: 'fr', name: 'Economy' },
    ],
    image: 'https://storage.googleapis.com/jaride-pro.firebasestorage.app/service_images/economy.png',
    kmCharge: '150',
  },
  {
    id: 'premium',
    names: [
      { languageName: 'en', name: 'Premium' },
      { languageName: 'yo', name: 'Premium' },
      { languageName: 'ha', name: 'Premium' },
      { languageName: 'ig', name: 'Premium' },
      { languageName: 'ar', name: 'Premium' },
      { languageName: 'fr', name: 'Premium' },
    ],
    image: 'https://storage.googleapis.com/jaride-pro.firebasestorage.app/service_images/premium.png',
    kmCharge: '250',
  },
  {
    id: 'suv',
    names: [
      { languageName: 'en', name: 'SUV' },
      { languageName: 'yo', name: 'SUV' },
      { languageName: 'ha', name: 'SUV' },
      { languageName: 'ig', name: 'SUV' },
      { languageName: 'ar', name: 'SUV' },
      { languageName: 'fr', name: 'SUV' },
    ],
    image: 'https://storage.googleapis.com/jaride-pro.firebasestorage.app/service_images/suv.png',
    kmCharge: '300',
  },
];

async function run() {
  console.log('=== Creating intercityService documents ===');
  for (const svc of services) {
    const docId = `intercity_${svc.id}`;
    const doc = {
      id: docId,
      enable: true,
      image: svc.image,
      kmCharge: svc.kmCharge,
      name: svc.names,
      offerRate: false,
      adminCommission: {
        isEnabled: true,
        amount: '10',
        type: 'percent',
      },
    };
    await db.collection('intercityService').doc(docId).set(doc);
    console.log(`  Created intercityService/${docId} — kmCharge: ₦${svc.kmCharge}/km`);
  }

  console.log('\n=== Enabling intercityType on service documents ===');
  for (const svc of services) {
    const snap = await db.collection('service').where('id', '==', svc.id).get();
    if (snap.empty) {
      console.log(`  WARNING: No service document with id="${svc.id}" found`);
      continue;
    }
    for (const doc of snap.docs) {
      await doc.ref.update({ intercityType: true });
      console.log(`  Updated service/${doc.id} → intercityType: true`);
    }
  }

  console.log('\nDone. OutStation is now fully enabled for Economy, Premium, SUV.');
  process.exit(0);
}

run().catch(e => { console.error(e); process.exit(1); });
