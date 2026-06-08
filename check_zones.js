const admin = require('./node_modules/firebase-admin');
const sa = require('./storage/app/firebase/credentials.json');
if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(sa), storageBucket: 'jaride-pro.firebasestorage.app' });
}
const db = admin.firestore();

async function run() {
  console.log('=== zones collection ===');
  const zones = await db.collection('zones').get();
  if (zones.empty) {
    console.log('EMPTY');
  } else {
    zones.docs.forEach(d => console.log(JSON.stringify({ id: d.id, ...d.data() })));
  }

  console.log('\n=== service prices (economy) ===');
  const svc = await db.collection('service').where('id', '==', 'economy').get();
  if (!svc.empty) {
    const data = svc.docs[0].data();
    console.log('prices:', JSON.stringify(data.prices));
  }
  process.exit(0);
}
run().catch(e => { console.error(e); process.exit(1); });
