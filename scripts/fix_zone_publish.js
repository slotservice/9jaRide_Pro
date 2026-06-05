// Adds publish:true to existing zone docs so the apps' .where('publish', isEqualTo:true) returns them.
const admin = require('firebase-admin');
if (!admin.apps.length) {
  admin.initializeApp({
    credential: admin.credential.cert(require('./firebase-admin-key.json')),
    databaseURL: 'https://jaride-pro-default-rtdb.firebaseio.com',
  });
}
const db = admin.firestore();

(async () => {
  const ids = ['lagos', 'abuja', 'port_harcourt'];
  for (const id of ids) {
    const ref = db.collection('zone').doc(id);
    const snap = await ref.get();
    if (!snap.exists) { console.log(`  SKIP ${id} (not found)`); continue; }
    await ref.update({ publish: true });
    console.log(`  OK   ${id} -> publish:true`);
  }
  console.log('Done.');
  process.exit(0);
})().catch(e => { console.error(e); process.exit(1); });
