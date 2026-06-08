// Run this on the VPS:
// cp /tmp/fix_service_images.js /var/www/admin/fix_service_images.js
// cd /var/www/admin && node fix_service_images.js

const admin = require('./node_modules/firebase-admin');
const sa = require('./storage/app/firebase/credentials.json');

admin.initializeApp({
  credential: admin.credential.cert(sa),
  storageBucket: 'jaride-pro.firebasestorage.app',
});

const db = admin.firestore();
const bucket = admin.storage().bucket();

async function run() {
  // 1. List current service documents and their image URLs
  const snap = await db.collection('service').get();
  console.log('\n=== Current service image URLs ===');
  for (const doc of snap.docs) {
    const d = doc.data();
    console.log(`[${doc.id}] type:${d.type || 'null'} image:${d.image}`);
  }

  // 2. Get public download URLs from Firebase Storage for the car photos
  const files = [
    { storagePath: 'service_images/economy.png' },
    { storagePath: 'service_images/premium.png' },
    { storagePath: 'service_images/suv.png' },
  ];

  console.log('\n=== Generating signed URLs for uploaded car photos ===');
  const urlMap = {};
  for (const f of files) {
    try {
      const file = bucket.file(f.storagePath);
      const [exists] = await file.exists();
      if (!exists) {
        console.log(`  MISSING: ${f.storagePath}`);
        continue;
      }
      // Make public and get URL
      await file.makePublic();
      const publicUrl = `https://storage.googleapis.com/${bucket.name}/${f.storagePath}`;
      urlMap[f.storagePath] = publicUrl;
      console.log(`  ${f.storagePath} → ${publicUrl}`);
    } catch (e) {
      console.error(`  ERROR on ${f.storagePath}:`, e.message);
    }
  }

  // 3. Match services to car photos by title and update image URL
  // Mapping: service title keywords → storage path
  const titleMap = [
    { keywords: ['economy', 'Economy', 'ECONOMY'], path: 'service_images/economy.png' },
    { keywords: ['premium', 'Premium', 'PREMIUM'], path: 'service_images/premium.png' },
    { keywords: ['suv', 'SUV', 'comfort', 'Comfort', 'COMFORT'], path: 'service_images/suv.png' },
  ];

  console.log('\n=== Updating service image URLs in Firestore ===');
  for (const doc of snap.docs) {
    const d = doc.data();
    if (d.type === 'assist') {
      console.log(`  SKIP [${doc.id}] — assist service`);
      continue;
    }

    // Get English title
    const titleArr = d.title || [];
    const enTitle = titleArr.find(t => t.type === 'en')?.name || '';
    const titleLower = enTitle.toLowerCase();

    let matchedPath = null;
    for (const m of titleMap) {
      if (m.keywords.some(k => titleLower.includes(k.toLowerCase()))) {
        matchedPath = m.path;
        break;
      }
    }

    if (!matchedPath || !urlMap[matchedPath]) {
      console.log(`  SKIP [${doc.id}] "${enTitle}" — no matching image`);
      continue;
    }

    const newUrl = urlMap[matchedPath];
    await db.collection('service').doc(doc.id).update({ image: newUrl });
    console.log(`  UPDATED [${doc.id}] "${enTitle}" → ${newUrl}`);
  }

  console.log('\nDone.');
  process.exit(0);
}

run().catch(e => { console.error(e); process.exit(1); });
