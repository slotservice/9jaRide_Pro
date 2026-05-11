/**
 * 9jaRide Pro — App Icon Generator
 * Generates branded icons with Deep Green background + Gold "9J" text
 * for all Android mipmap density buckets.
 */

const { createCanvas } = require('canvas');
const fs = require('fs');
const path = require('path');

// Brand colors
const DEEP_GREEN = '#1B5E20';
const GOLD = '#D4AF37';
const DARK_GREEN = '#0D3B0F';
const WHITE = '#FFFFFF';

// Android mipmap sizes
const SIZES = {
  'mipmap-mdpi': 48,
  'mipmap-hdpi': 72,
  'mipmap-xhdpi': 96,
  'mipmap-xxhdpi': 144,
  'mipmap-xxxhdpi': 192,
};

// Adaptive icon sizes (foreground/background are 108dp-based, rendered at 1.5x)
const ADAPTIVE_SIZES = {
  'mipmap-mdpi': 108,
  'mipmap-hdpi': 162,
  'mipmap-xhdpi': 216,
  'mipmap-xxhdpi': 324,
  'mipmap-xxxhdpi': 432,
};

function drawIcon(size, isAdaptive = false) {
  const canvas = createCanvas(size, size);
  const ctx = canvas.getContext('2d');

  if (isAdaptive) {
    // Adaptive icon: content in center 66% (safe zone)
    const safeZone = size * 0.66;
    const offset = (size - safeZone) / 2;

    // Transparent background (handled separately)
    ctx.clearRect(0, 0, size, size);

    // Draw "9J" text centered in safe zone
    const fontSize = safeZone * 0.45;
    ctx.font = `bold ${fontSize}px Arial, Helvetica, sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    // Gold text with slight shadow
    ctx.shadowColor = 'rgba(0,0,0,0.3)';
    ctx.shadowBlur = fontSize * 0.05;
    ctx.shadowOffsetY = fontSize * 0.03;
    ctx.fillStyle = GOLD;
    ctx.fillText('9J', size / 2, size / 2 - fontSize * 0.05);

    // Small "PRO" text below
    const proSize = fontSize * 0.28;
    ctx.font = `bold ${proSize}px Arial, Helvetica, sans-serif`;
    ctx.shadowBlur = proSize * 0.05;
    ctx.fillStyle = WHITE;
    ctx.fillText('PRO', size / 2, size / 2 + fontSize * 0.45);
  } else {
    // Legacy icon: rounded rect with full design
    const radius = size * 0.18;

    // Background with gradient effect
    ctx.beginPath();
    ctx.roundRect(0, 0, size, size, radius);
    ctx.fillStyle = DEEP_GREEN;
    ctx.fill();

    // Subtle gradient overlay
    const grad = ctx.createLinearGradient(0, 0, size, size);
    grad.addColorStop(0, 'rgba(255,255,255,0.1)');
    grad.addColorStop(1, 'rgba(0,0,0,0.15)');
    ctx.fillStyle = grad;
    ctx.fill();

    // "9J" text
    const fontSize = size * 0.38;
    ctx.font = `bold ${fontSize}px Arial, Helvetica, sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    ctx.shadowColor = 'rgba(0,0,0,0.4)';
    ctx.shadowBlur = fontSize * 0.06;
    ctx.shadowOffsetY = fontSize * 0.04;
    ctx.fillStyle = GOLD;
    ctx.fillText('9J', size / 2, size / 2 - fontSize * 0.12);

    // "PRO" text
    const proSize = fontSize * 0.28;
    ctx.font = `bold ${proSize}px Arial, Helvetica, sans-serif`;
    ctx.shadowBlur = proSize * 0.04;
    ctx.fillStyle = WHITE;
    ctx.fillText('PRO', size / 2, size / 2 + fontSize * 0.42);
  }

  return canvas.toBuffer('image/png');
}

function drawBackground(size) {
  const canvas = createCanvas(size, size);
  const ctx = canvas.getContext('2d');

  // Solid deep green background
  ctx.fillStyle = DEEP_GREEN;
  ctx.fillRect(0, 0, size, size);

  // Subtle gradient
  const grad = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size * 0.7);
  grad.addColorStop(0, 'rgba(255,255,255,0.08)');
  grad.addColorStop(1, 'rgba(0,0,0,0.05)');
  ctx.fillStyle = grad;
  ctx.fillRect(0, 0, size, size);

  return canvas.toBuffer('image/png');
}

function drawMonochrome(size) {
  const canvas = createCanvas(size, size);
  const ctx = canvas.getContext('2d');

  ctx.clearRect(0, 0, size, size);

  const safeZone = size * 0.66;
  const fontSize = safeZone * 0.45;
  ctx.font = `bold ${fontSize}px Arial, Helvetica, sans-serif`;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillStyle = '#000000';
  ctx.fillText('9J', size / 2, size / 2 - fontSize * 0.05);

  const proSize = fontSize * 0.28;
  ctx.font = `bold ${proSize}px Arial, Helvetica, sans-serif`;
  ctx.fillText('PRO', size / 2, size / 2 + fontSize * 0.45);

  return canvas.toBuffer('image/png');
}

function generateForApp(appPath, appName) {
  console.log(`\nGenerating icons for ${appName}...`);
  const resDir = path.join(appPath, 'android', 'app', 'src', 'main', 'res');

  for (const [folder, size] of Object.entries(SIZES)) {
    const dir = path.join(resDir, folder);
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });

    // Legacy icon
    fs.writeFileSync(path.join(dir, 'ic_launcher.png'), drawIcon(size, false));
    console.log(`  ${folder}/ic_launcher.png (${size}x${size})`);
  }

  for (const [folder, size] of Object.entries(ADAPTIVE_SIZES)) {
    const dir = path.join(resDir, folder);

    // Foreground (text only, transparent bg)
    fs.writeFileSync(path.join(dir, 'ic_launcher_foreground.png'), drawIcon(size, true));

    // Background (solid green)
    fs.writeFileSync(path.join(dir, 'ic_launcher_background.png'), drawBackground(size));

    // Monochrome (Android 13+)
    fs.writeFileSync(path.join(dir, 'ic_launcher_monochrome.png'), drawMonochrome(size));

    console.log(`  ${folder}/ic_launcher_{foreground,background,monochrome}.png (${size}x${size})`);
  }

  console.log(`  Done: ${appName}`);
}

// Generate for all 3 apps
const appsDir = 'd:/Freelancer-Project/Oladoyin/GoRide_V5.6_Source_Code/Applications/GoRide-5.6';

generateForApp(path.join(appsDir, 'customer'), 'Customer');
generateForApp(path.join(appsDir, 'driver'), 'Driver');
generateForApp(path.join(appsDir, 'owner'), 'Owner');

console.log('\n=== All icons generated! ===');
