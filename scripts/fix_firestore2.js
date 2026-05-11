/**
 * 9jaRide Pro — Fix remaining Firestore issues
 * - Enable Paystack in sandbox mode for wallet topup testing
 * - Add subscription_model field to globalValue
 */

const admin = require('firebase-admin');

if (!admin.apps.length) {
  const serviceAccount = require('./firebase-admin-key.json');
  admin.initializeApp({
    credential: admin.credential.cert(serviceAccount),
    databaseURL: 'https://jaride-pro-default-rtdb.firebaseio.com',
  });
}

const db = admin.firestore();

async function fix() {
  console.log('Applying Firestore fixes (round 2)...\n');

  // 1. Add subscription_model field to globalValue
  console.log('1. Adding subscription_model to settings/globalValue...');
  await db.collection('settings').doc('globalValue').update({
    subscription_model: false,
  });
  console.log('   Done.\n');

  // 2. Update adminCommission with correct structure
  console.log('2. Fixing settings/adminCommission...');
  await db.collection('settings').doc('adminCommission').set({
    amount: '10',
    isEnabled: true,
    type: 'percentage',
  });
  console.log('   Done.\n');

  // 3. Enable Paystack in sandbox mode for testing wallet topup
  console.log('3. Enabling Paystack sandbox for testing...');
  await db.collection('settings').doc('payment').update({
    'payStack.enable': true,
    'payStack.isSandbox': true,
    'payStack.publicKey': 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'payStack.secretKey': 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'payStack.callbackURL': 'https://9jaridepro.com/paystack/callback',
    'payStack.webhookURL': 'https://9jaridepro.com/paystack/webhook',
  });
  console.log('   Note: Replace pk_test/sk_test with real Paystack sandbox keys from client.\n');

  console.log('=== Fixes applied! ===');
  process.exit(0);
}

fix().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
