/**
 * 9jaRide Pro — Firestore Settings Populate Script
 *
 * Populates all required Firestore collections for the apps to function.
 *
 * Usage:
 *   1. Place your Firebase service account JSON as firebase-admin-key.json in this directory
 *   2. npm install firebase-admin
 *   3. node populate_firestore.js
 *
 * WARNING: This will OVERWRITE existing settings documents. Run only on first setup
 * or when you need to reset to defaults.
 */

const admin = require('firebase-admin');

// Initialize Firebase Admin with service account
const serviceAccount = require('./firebase-admin-key.json');

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: 'https://jaride-pro-default-rtdb.firebaseio.com',
});

const db = admin.firestore();

async function populate() {
  console.log('Starting Firestore population for 9jaRide Pro...\n');

  // ─── settings/globalValue ───────────────────────────────────
  console.log('1. Writing settings/globalValue...');
  await db.collection('settings').doc('globalValue').set({
    defaultCountryCode: '+234',          // Nigeria
    app_customer_color: '#1B5E20',       // Deep Green (9jaRide brand)
    app_customer_light_color: '#4CAF50', // Light Green
    distanceType: 'km',
    radius: '15',                        // 15km search radius
    mapType: 'google',
    selectedMapType: 0,
    driverLocationUpdate: 10,            // seconds
    regionCode: 'NG',
    regionCountry: 'Nigeria',
  });
  console.log('   Done.\n');

  // ─── settings/globalKey ─────────────────────────────────────
  console.log('2. Writing settings/globalKey...');
  await db.collection('settings').doc('globalKey').set({
    googleMapKey: 'AIzaSyCWXkyRu5fIX-wxCy1E9_Nx0FqnVvKvKL0',
  });
  console.log('   Done.\n');

  // ─── settings/adminCommission ───────────────────────────────
  console.log('3. Writing settings/adminCommission...');
  await db.collection('settings').doc('adminCommission').set({
    amount: '10',
    isEnabled: true,
    type: 'percentage',
  });
  console.log('   Done.\n');

  // ─── settings/referral ──────────────────────────────────────
  console.log('4. Writing settings/referral...');
  await db.collection('settings').doc('referral').set({
    referralAmount: '500',      // NGN
    referralAmountDriver: '500',
  });
  console.log('   Done.\n');

  // ─── settings/maintenance_settings ──────────────────────────
  console.log('5. Writing settings/maintenance_settings...');
  await db.collection('settings').doc('maintenance_settings').set({
    customerApp: false,
    ownerApp: false,
    driverApp: false,
  });
  console.log('   Done.\n');

  // ─── settings/contact_us ────────────────────────────────────
  console.log('6. Writing settings/contact_us...');
  await db.collection('settings').doc('contact_us').set({
    supportURL: 'https://9jaridepro.com/support',
  });
  console.log('   Done.\n');

  // ─── settings/global ────────────────────────────────────────
  console.log('7. Writing settings/global...');
  await db.collection('settings').doc('global').set({
    appVersion: '1.0.0',
    privacyPolicy: [
      { privacyPolicy: 'https://9jaridepro.com/privacy', type: 'en' },
    ],
    termsAndConditions: [
      { termsAndConditions: 'https://9jaridepro.com/terms', type: 'en' },
    ],
  });
  console.log('   Done.\n');

  // ─── settings/notification_setting ──────────────────────────
  console.log('8. Writing settings/notification_setting...');
  await db.collection('settings').doc('notification_setting').set({
    senderId: '872495882111',
    serviceJson: '',  // Set after uploading service account to storage
  });
  console.log('   Done.\n');

  // ─── settings/hirePurchase ──────────────────────────────────
  console.log('9. Writing settings/hirePurchase...');
  await db.collection('settings').doc('hirePurchase').set({
    defaultDailyDeduction: 500,   // NGN
    yellowThresholdHours: 24,
    redThresholdHours: 48,
    autoKillSwitch: true,
    royaltyPercentage: 2.5,
  });
  console.log('   Done.\n');

  // ─── settings/payment ───────────────────────────────────────
  console.log('10. Writing settings/payment...');
  await db.collection('settings').doc('payment').set({
    strip: {
      isEnabled: false,
      isSandbox: true,
      clientpublishableKey: '',
      secretKey: '',
    },
    paypal: {
      isEnabled: false,
      isSandbox: true,
      paypalClient: '',
      paypalSecret: '',
    },
    razorpay: {
      isEnabled: false,
      isSandbox: true,
      razorpayKey: '',
      razorpaySecret: '',
    },
    payStack: {
      isEnabled: false,
      isSandbox: true,
      payStackSecret: '',
      payStackPublic: '',
    },
    flutterWave: {
      isEnabled: false,
      isSandbox: true,
      flutterwavePublic: '',
      flutterwaveSecret: '',
      flutterwaveEncryption: '',
    },
    wallet: {
      isEnabled: true,
    },
    cod: {
      isEnabled: true,
    },
  });
  console.log('   Done.\n');

  // ─── currency collection ────────────────────────────────────
  console.log('11. Writing currency collection...');
  await db.collection('currency').doc('NGN').set({
    id: 'NGN',
    code: 'NGN',
    symbol: '\u20A6',             // Naira symbol
    name: 'Nigerian Naira',
    symbolAtRight: false,
    decimalDigits: 2,
    enable: true,
    createdAt: admin.firestore.FieldValue.serverTimestamp(),
    updatedAt: admin.firestore.FieldValue.serverTimestamp(),
  });
  // Also add USD as fallback
  await db.collection('currency').doc('USD').set({
    id: 'USD',
    code: 'USD',
    symbol: '$',
    name: 'US Dollar',
    symbolAtRight: false,
    decimalDigits: 2,
    enable: false,
    createdAt: admin.firestore.FieldValue.serverTimestamp(),
    updatedAt: admin.firestore.FieldValue.serverTimestamp(),
  });
  console.log('   Done.\n');

  // ─── service collection (vehicle types) ─────────────────────
  console.log('12. Writing service collection (vehicle types)...');

  await db.collection('service').doc('economy').set({
    id: 'economy',
    image: '',
    enable: true,
    offerRate: true,
    intercityType: false,
    markerIcon: '',
    title: [{ name: 'Economy', language_code: 'en' }],
    adminCommission: { isEnabled: true, amount: '10', type: 'percentage' },
    prices: [
      {
        zoneId: 'default',
        basicFare: '300',
        basicFareCharge: '0',
        kmCharge: '50',
        perMinuteCharge: '5',
        holdingMinute: '5',
        holdingMinuteCharge: '10',
        acCharge: '0',
        nonAcCharge: '0',
        isAcNonAc: false,
        nightCharge: '20',
        startNightTime: '22:00',
        endNightTime: '06:00',
      },
    ],
  });

  await db.collection('service').doc('premium').set({
    id: 'premium',
    image: '',
    enable: true,
    offerRate: true,
    intercityType: false,
    markerIcon: '',
    title: [{ name: 'Premium', language_code: 'en' }],
    adminCommission: { isEnabled: true, amount: '15', type: 'percentage' },
    prices: [
      {
        zoneId: 'default',
        basicFare: '500',
        basicFareCharge: '0',
        kmCharge: '80',
        perMinuteCharge: '10',
        holdingMinute: '5',
        holdingMinuteCharge: '15',
        acCharge: '50',
        nonAcCharge: '0',
        isAcNonAc: true,
        nightCharge: '25',
        startNightTime: '22:00',
        endNightTime: '06:00',
      },
    ],
  });

  await db.collection('service').doc('suv').set({
    id: 'suv',
    image: '',
    enable: true,
    offerRate: true,
    intercityType: false,
    markerIcon: '',
    title: [{ name: 'SUV', language_code: 'en' }],
    adminCommission: { isEnabled: true, amount: '15', type: 'percentage' },
    prices: [
      {
        zoneId: 'default',
        basicFare: '600',
        basicFareCharge: '0',
        kmCharge: '100',
        perMinuteCharge: '12',
        holdingMinute: '5',
        holdingMinuteCharge: '20',
        acCharge: '0',
        nonAcCharge: '0',
        isAcNonAc: false,
        nightCharge: '30',
        startNightTime: '22:00',
        endNightTime: '06:00',
      },
    ],
  });
  console.log('   Done.\n');

  // ─── languages collection ───────────────────────────────────
  console.log('13. Writing languages collection...');
  await db.collection('languages').doc('en').set({
    code: 'en',
    name: 'English',
    isDefault: true,
    enable: true,
  });
  console.log('   Done.\n');

  console.log('=== Firestore population complete! ===');
  console.log('\nNext steps:');
  console.log('  - Update payment keys in settings/payment when client provides them');
  console.log('  - Upload service vehicle images and update image URLs');
  console.log('  - Populate landingPageTemplate collection for the landing page');
  console.log('  - Add Sendbird settings if using in-app chat');

  process.exit(0);
}

populate().catch((err) => {
  console.error('FATAL ERROR:', err);
  process.exit(1);
});
