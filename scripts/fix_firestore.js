/**
 * 9jaRide Pro — Fix Firestore data issues
 * Fixes: payment model structure, subscription plans, service images
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
  console.log('Fixing Firestore data...\n');

  // ─── 1. Fix settings/payment (correct field names for PaymentModel) ─────
  console.log('1. Fixing settings/payment...');
  await db.collection('settings').doc('payment').set({
    cash: {
      enable: true,
      name: 'Cash',
    },
    wallet: {
      enable: true,
      name: 'Wallet',
    },
    strip: {
      enable: false,
      name: 'Stripe',
      clientpublishableKey: '',
      stripeSecret: '',
      isSandbox: true,
    },
    paypal: {
      enable: false,
      name: 'PayPal',
      paypalClient: '',
      paypalSecret: '',
      isSandbox: true,
      image: '',
    },
    payStack: {
      enable: false,
      name: 'PayStack',
      secretKey: '',
      publicKey: '',
      callbackURL: '',
      webhookURL: '',
      isSandbox: true,
    },
    flutterWave: {
      enable: false,
      name: 'FlutterWave',
      publicKey: '',
      secretKey: '',
      encryptionKey: '',
      isSandbox: true,
    },
    razorpay: {
      enable: false,
      name: 'Razorpay',
      razorpayKey: '',
      razorpaySecret: '',
      isSandbox: true,
    },
    mercadoPago: {
      enable: false,
      name: 'MercadoPago',
      publicKey: '',
      accessToken: '',
      isSandbox: true,
    },
    paytm: {
      enable: false,
      name: 'Paytm',
      paytmMID: '',
      merchantKey: '',
      isSandbox: true,
    },
    payfast: {
      enable: false,
      name: 'PayFast',
      merchantId: '',
      merchantKey: '',
      return_url: '',
      cancel_url: '',
      notify_url: '',
      isSandbox: true,
    },
    xendit: {
      enable: false,
      name: 'Xendit',
      apiKey: '',
      isSandbox: true,
      image: '',
    },
    midtrans: {
      enable: false,
      name: 'Midtrans',
      serverKey: '',
      isSandbox: true,
      image: '',
    },
    orangePay: {
      enable: false,
      name: 'Orange Money',
      clientId: '',
      clientSecret: '',
      merchantKey: '',
      auth: '',
      return_url: '',
      cancel_url: '',
      notif_url: '',
      isSandbox: true,
      image: '',
    },
  });
  console.log('   Done.\n');

  // ─── 2. Add subscription_plans collection ───────────────────────────────
  console.log('2. Adding subscription_plans...');

  await db.collection('subscription_plans').doc('free_plan').set({
    id: 'free_plan',
    name: 'Free Plan',
    description: 'Get started with 9jaRide Pro. Basic access to accept rides.',
    price: '0',
    expiryDay: '-1',
    bookingLimit: '-1',
    driverLimit: '-1',
    isEnable: true,
    place: '1',
    image: '',
    type: 'free',
    plan_points: [
      'Unlimited ride requests',
      'Basic driver profile',
      'Standard support',
    ],
    planFor: 'driver',
    createdAt: admin.firestore.FieldValue.serverTimestamp(),
  });

  await db.collection('subscription_plans').doc('standard_plan').set({
    id: 'standard_plan',
    name: 'Standard Plan',
    description: 'Priority access and more bookings per day.',
    price: '2000',
    expiryDay: '30',
    bookingLimit: '-1',
    driverLimit: '-1',
    isEnable: true,
    place: '2',
    image: '',
    type: 'standard',
    plan_points: [
      'Unlimited ride requests',
      'Priority in bidding',
      'Premium driver badge',
      'Priority support',
    ],
    planFor: 'driver',
    createdAt: admin.firestore.FieldValue.serverTimestamp(),
  });
  console.log('   Done.\n');

  // ─── 3. Fix adminCommission (disable subscription requirement for now) ──
  console.log('3. Updating adminCommission...');
  await db.collection('settings').doc('adminCommission').set({
    amount: '10',
    isEnabled: true,
    type: 'percentage',
    commissionSubscriptionID: 'free_plan',
  });
  console.log('   Done.\n');

  // ─── 4. Update service vehicle types with placeholder images ────────────
  console.log('4. Updating service vehicle images...');

  // We use a data URI approach - but Firestore stores URLs
  // For now set image to empty and the app will show placeholder
  // The real images should be uploaded to Firebase Storage later
  const services = ['economy', 'premium', 'suv'];
  for (const svc of services) {
    const doc = await db.collection('service').doc(svc).get();
    if (doc.exists) {
      console.log(`   ${svc}: exists, image field = "${doc.data().image || '(empty)'}"`);
    }
  }
  console.log('   Note: Vehicle images need to be uploaded to Firebase Storage.');
  console.log('   The gray box is expected until real vehicle images are provided.\n');

  // ─── 5. Verify settings/globalValue has correct fields ──────────────────
  console.log('5. Checking settings/globalValue...');
  const gv = await db.collection('settings').doc('globalValue').get();
  if (gv.exists) {
    console.log('   OK - exists with fields:', Object.keys(gv.data()).join(', '));
  }

  console.log('\n=== Firestore fixes complete! ===');
  console.log('\nFixed:');
  console.log('  - Payment model: correct field names (cash, wallet, strip, paypal, etc.)');
  console.log('  - Subscription plans: free_plan + standard_plan added');
  console.log('  - Admin commission: linked to free_plan');
  console.log('\nStill needed:');
  console.log('  - Upload vehicle type images to Firebase Storage');
  console.log('  - Payment gateway API keys when client provides them');

  process.exit(0);
}

fix().catch((err) => {
  console.error('ERROR:', err);
  process.exit(1);
});
