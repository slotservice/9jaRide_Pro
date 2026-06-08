const admin = require('./node_modules/firebase-admin');
const serviceAccount = require('./storage/app/firebase/credentials.json');
if (!admin.apps.length) {
  admin.initializeApp({ credential: admin.credential.cert(serviceAccount) });
}
const db = admin.firestore();
db.collection('intercityService').get().then(snap => {
  if (snap.empty) {
    console.log('NO DOCUMENTS in intercityService collection');
  } else {
    snap.docs.forEach(d => {
      const data = d.data();
      console.log(JSON.stringify({ id: d.id, name: data.name, image: data.image, enable: data.enable }));
    });
  }
  process.exit(0);
}).catch(e => { console.error(e); process.exit(1); });
