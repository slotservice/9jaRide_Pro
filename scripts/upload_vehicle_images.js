/**
 * 9jaRide Pro — Generate PNG vehicle images and upload to Firebase Storage.
 * Uses ImageMagick (convert) to create PNG car icons, then uploads via Admin SDK.
 *
 * Run on VPS: node upload_vehicle_images.js
 */

const admin = require('firebase-admin');
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const serviceAccount = require('./firebase-admin-key.json');

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  storageBucket: 'jaride-pro.firebasestorage.app',
});

const db = admin.firestore();
const bucket = admin.storage().bucket();

const vehicles = [
  {
    id: 'economy',
    label: 'ECONOMY',
    bgColor: '#607D8B',
    textColor: 'white',
    filename: 'economy.png',
  },
  {
    id: 'premium',
    label: 'PREMIUM',
    bgColor: '#1B5E20',
    textColor: '#D4AF37',
    filename: 'premium.png',
  },
  {
    id: 'suv',
    label: 'SUV',
    bgColor: '#263238',
    textColor: 'white',
    filename: 'suv.png',
  },
];

function generatePng(v, outPath) {
  // Draw a rounded vehicle card using ImageMagick:
  // - Colored background
  // - Simple car silhouette shape (body + wheels using draw primitives)
  // - Vehicle type label
  const cmd = [
    'convert',
    '-size 400x200',
    `xc:'${v.bgColor}'`,
    // Car body - main rectangle
    `-fill '${v.textColor}' -draw "roundrectangle 60,60 340,130 15,15"`,
    // Car roof
    `-fill '${v.textColor}' -draw "roundrectangle 110,30 290,75 12,12"`,
    // Windows (darker cutout effect)
    `-fill '${v.bgColor}' -draw "roundrectangle 120,38 195,70 8,8"`,
    `-fill '${v.bgColor}' -draw "roundrectangle 205,38 280,70 8,8"`,
    // Wheels
    `-fill '#111111' -draw "circle 115,132 115,160"`,
    `-fill '#111111' -draw "circle 285,132 285,160"`,
    `-fill '#444444' -draw "circle 115,132 115,150"`,
    `-fill '#444444' -draw "circle 285,132 285,150"`,
    // Headlights
    `-fill '#FFF176' -draw "roundrectangle 60,75 80,95 4,4"`,
    // Taillights
    `-fill '#EF5350' -draw "roundrectangle 320,75 340,95 4,4"`,
    // Label
    `-fill '${v.textColor}' -font DejaVu-Sans-Bold -pointsize 28 -gravity South -annotate +0+15 '${v.label}'`,
    `"${outPath}"`,
  ].join(' ');

  execSync(cmd, { stdio: 'pipe' });
}

async function run() {
  const tmpDir = '/tmp/vehicle_pngs';
  if (!fs.existsSync(tmpDir)) fs.mkdirSync(tmpDir);

  console.log('Generating and uploading vehicle PNG images...\n');

  for (const v of vehicles) {
    const outPath = path.join(tmpDir, v.filename);

    console.log(`Generating ${v.filename}...`);
    generatePng(v, outPath);

    console.log(`Uploading ${v.filename}...`);
    const file = bucket.file(`service_images/${v.filename}`);
    await file.save(fs.readFileSync(outPath), {
      contentType: 'image/png',
      metadata: { cacheControl: 'public, max-age=31536000' },
    });
    await file.makePublic();

    const publicUrl = `https://storage.googleapis.com/jaride-pro.firebasestorage.app/service_images/${v.filename}`;
    await db.collection('service').doc(v.id).update({ image: publicUrl });

    console.log(`  Done: ${publicUrl}\n`);
  }

  // Clean up old SVG files from storage
  try {
    for (const name of ['economy.svg', 'premium.svg', 'suv.svg']) {
      await bucket.file(`service_images/${name}`).delete();
    }
    console.log('Old SVG files removed from Storage.');
  } catch (_) {}

  fs.rmSync(tmpDir, { recursive: true });
  console.log('\nAll done.');
  process.exit(0);
}

run().catch((err) => {
  console.error(err);
  process.exit(1);
});
