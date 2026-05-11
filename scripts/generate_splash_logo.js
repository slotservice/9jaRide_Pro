/**
 * 9jaRide Pro — Splash Screen Logo Generator
 * Replaces the GoRide app_logo.png with 9jaRide Pro branding
 */

const { createCanvas } = require('canvas');
const fs = require('fs');
const path = require('path');

const DEEP_GREEN = '#1B5E20';
const GOLD = '#D4AF37';
const WHITE = '#FFFFFF';

function generateSplashLogo() {
  // Match approximate size of original GoRide logo (536x296 based on aspect ratio)
  const width = 536;
  const height = 296;
  const canvas = createCanvas(width, height);
  const ctx = canvas.getContext('2d');

  // Transparent background
  ctx.clearRect(0, 0, width, height);

  // Draw "9ja" in large bold gold text
  const mainSize = 120;
  ctx.font = `bold ${mainSize}px Arial, Helvetica, sans-serif`;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';

  // Shadow for depth
  ctx.shadowColor = 'rgba(0,0,0,0.3)';
  ctx.shadowBlur = 8;
  ctx.shadowOffsetY = 3;

  // "9ja" in gold
  ctx.fillStyle = GOLD;
  const ninejaWidth = ctx.measureText('9ja').width;

  // "Ride" in white
  const rideWidth = ctx.measureText('Ride').width;

  // Calculate positions to center "9jaRide" as one word
  const totalWidth = ninejaWidth + rideWidth;
  const startX = (width - totalWidth) / 2;

  ctx.textAlign = 'left';
  ctx.fillStyle = GOLD;
  ctx.fillText('9ja', startX, height * 0.38);

  ctx.fillStyle = WHITE;
  ctx.fillText('Ride', startX + ninejaWidth, height * 0.38);

  // "PRO" text below in smaller gold with letter spacing
  const proSize = 48;
  ctx.font = `bold ${proSize}px Arial, Helvetica, sans-serif`;
  ctx.textAlign = 'center';
  ctx.shadowBlur = 4;
  ctx.shadowOffsetY = 2;
  ctx.fillStyle = GOLD;
  // Add letter spacing effect
  const proText = 'P  R  O';
  ctx.fillText(proText, width / 2, height * 0.68);

  // Small location pin icon (circle + triangle) next to the text
  ctx.shadowBlur = 0;
  ctx.shadowOffsetY = 0;

  // Green circle pin
  const pinX = width * 0.82;
  const pinY = height * 0.3;
  const pinRadius = 18;

  ctx.beginPath();
  ctx.arc(pinX, pinY, pinRadius, 0, Math.PI * 2);
  ctx.fillStyle = DEEP_GREEN;
  ctx.fill();

  // White dot in center of pin
  ctx.beginPath();
  ctx.arc(pinX, pinY - 3, 6, 0, Math.PI * 2);
  ctx.fillStyle = WHITE;
  ctx.fill();

  // Pin point (triangle)
  ctx.beginPath();
  ctx.moveTo(pinX - 10, pinY + 10);
  ctx.lineTo(pinX + 10, pinY + 10);
  ctx.lineTo(pinX, pinY + 28);
  ctx.closePath();
  ctx.fillStyle = DEEP_GREEN;
  ctx.fill();

  // Speed lines (3 horizontal lines) to the right of pin
  ctx.strokeStyle = 'rgba(255,255,255,0.5)';
  ctx.lineWidth = 3;
  ctx.lineCap = 'round';

  for (let i = 0; i < 3; i++) {
    const lineY = pinY - 8 + (i * 12);
    const lineLen = 25 - (i * 5);
    ctx.beginPath();
    ctx.moveTo(pinX + pinRadius + 8, lineY);
    ctx.lineTo(pinX + pinRadius + 8 + lineLen, lineY);
    ctx.stroke();
  }

  // Thin underline decoration
  ctx.strokeStyle = GOLD;
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(width * 0.3, height * 0.82);
  ctx.lineTo(width * 0.7, height * 0.82);
  ctx.stroke();

  return canvas.toBuffer('image/png');
}

// Generate and save to all 3 apps
const appsDir = 'd:/Freelancer-Project/Oladoyin/GoRide_V5.6_Source_Code/Applications/GoRide-5.6';
const logo = generateSplashLogo();

const apps = ['customer', 'driver', 'owner'];
apps.forEach(app => {
  const dest = path.join(appsDir, app, 'assets', 'app_logo.png');
  if (fs.existsSync(path.dirname(dest))) {
    fs.writeFileSync(dest, logo);
    console.log(`Written: ${app}/assets/app_logo.png`);
  } else {
    console.log(`Skipped: ${app}/assets/ directory not found`);
  }
});

console.log('\nSplash logo generated for all apps!');
