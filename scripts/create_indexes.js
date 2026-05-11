/**
 * 9jaRide Pro — Create required Firestore composite indexes
 *
 * These indexes are REQUIRED for the app queries to work.
 * Unfortunately, composite indexes cannot be created via Admin SDK.
 *
 * You MUST create them manually in Firebase Console:
 * https://console.firebase.google.com/project/jaride-pro/firestore/indexes
 *
 * OR deploy via firebase CLI with firestore.indexes.json
 *
 * REQUIRED INDEXES:
 */

const indexes = [
  {
    collection: 'orders',
    fields: ['userId (Asc)', 'status (Asc)', 'paymentStatus (Asc)', 'createdDate (Desc)'],
    reason: 'Active Rides tab - customer app',
  },
  {
    collection: 'orders',
    fields: ['userId (Asc)', 'status (Asc)', 'paymentStatus (Asc)', 'createdDate (Desc)'],
    reason: 'Completed Rides tab - customer app',
  },
  {
    collection: 'orders',
    fields: ['userId (Asc)', 'status (Asc)', 'createdDate (Desc)'],
    reason: 'Canceled Rides tab - customer app',
  },
  {
    collection: 'orders_intercity',
    fields: ['userId (Asc)', 'status (Asc)', 'paymentStatus (Asc)', 'createdDate (Desc)'],
    reason: 'OutStation Active Rides - customer app',
  },
  {
    collection: 'orders_intercity',
    fields: ['userId (Asc)', 'status (Asc)', 'createdDate (Desc)'],
    reason: 'OutStation Canceled Rides - customer app',
  },
  {
    collection: 'chat',
    fields: ['sender_receiver_id (Array)', 'createdAt (Desc)'],
    reason: 'Inbox screen - customer & driver app',
  },
  {
    collection: 'subscription_plans',
    fields: ['isEnable (Asc)', 'place (Asc)'],
    reason: 'Subscription plan list - driver app',
  },
  {
    collection: 'orders',
    fields: ['driverId (Asc)', 'status (Asc)', 'paymentStatus (Asc)', 'createdDate (Desc)'],
    reason: 'Driver rides list - driver app',
  },
];

console.log('=== REQUIRED FIRESTORE COMPOSITE INDEXES ===\n');
console.log('Go to: https://console.firebase.google.com/project/jaride-pro/firestore/indexes\n');
console.log('Click "Create Index" for each one below:\n');

indexes.forEach((idx, i) => {
  console.log(`--- Index ${i + 1}: ${idx.reason} ---`);
  console.log(`Collection: ${idx.collection}`);
  console.log(`Fields: ${idx.fields.join(', ')}`);
  console.log('');
});

console.log('\nAlternatively, the app will auto-generate index links in the');
console.log('Firebase Console error logs when the queries fail.');
console.log('Check: Firebase Console → Firestore → Indexes tab for suggested indexes.');
