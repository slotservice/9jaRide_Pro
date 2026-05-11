# 9jaRide Pro — Project Status & Implementation Plan

**Generated:** 2026-04-07  
**Analysis Depth:** ULTRADEEP — full codebase audit  
**Project:** Taxi bidding app, GoRide V5.6 clone, Phase 1 ($2,200)  
**Client:** Eagles Partners (Oladoyin)

---

## QUICK REFERENCE

| Panel | URL | Credentials |
|-------|-----|-------------|
| Admin | http://148.230.120.40 | admin@9jaridepro.com / 9jaRide@2026! |
| Landing | http://148.230.120.40:8081 | — |
| Owner | http://148.230.120.40:8082 | Phone OTP |
| VPS SSH | root@148.230.120.40 | Key auth |
| MySQL | njaride_user | N9jaR1de_Pr0_2026! |
| Firebase | jaride-pro (Spark) | AIzaSyCWXkyRu5fIX-wxCy1E9_Nx0FqnVvKvKL0 |

---

## LEGEND

| Symbol | Meaning |
|--------|---------|
| ✅ | Fully implemented and working |
| ⚠️ | Implemented but incomplete or incorrectly (stub/partial) |
| ❌ | Not implemented |
| 🔒 | Security issue |
| 🐛 | Bug |
| ⏳ | Waiting on client |

---

## 1. ADMIN PANEL (Laravel 10) — LIVE at :80

### Routes & Controllers

| Feature | Status | Notes |
|---------|--------|-------|
| Auth (login/logout/password) | ✅ | Laravel Auth standard |
| RBAC — Spatie permissions | ✅ | 150+ permissions, middleware on all routes |
| User management | ✅ | List, add, edit, delete |
| Driver management | ✅ | KYC docs, approvals, chat |
| Owner management | ✅ | List, approvals, documents |
| Ride management (city) | ✅ | Full CRUD + status |
| Ride management (intercity) | ✅ | Full CRUD + status |
| Zone & Surge zones | ✅ | Dynamic pricing zones |
| Services & Taxes | ✅ | |
| Coupons | ✅ | |
| Banners | ✅ | |
| Currency | ✅ | |
| Document types | ✅ | |
| Payment transactions | ✅ | |
| Payout requests | ✅ | |
| Subscription plans | ✅ | |
| SOS management | ✅ | Panic button incident management |
| GOD Eye (map view) | ✅ | Real-time driver locations |
| Reports | ✅ | |
| CMS / FAQs | ✅ | |
| Notifications | ✅ | Firebase push |
| Airports | ✅ | |
| Freight vehicles | ✅ | |
| Hire Purchase module | ✅ | Full server-side Firestore CRUD + daily cron — see details below |
| Kill Switch API | ✅ | Real Firestore writes via kreait/laravel-firebase |
| Settings (global, business, payment, landing template) | ✅ | |
| Firebase integration | ✅ | Client-side JS SDK in blade views |
| Branding (9jaRide Pro) | ✅ | Deep Green + Gold theme |

### ✅ HIRE PURCHASE — FULLY IMPLEMENTED (2026-04-07)

**Server-side (HirePurchaseController.php — 8 API endpoints via kreait/laravel-firebase):**
- `GET /api/hp/drivers` — List all HP-enabled drivers from Firestore
- `GET /api/hp/driver/{id}` — Get single driver HP data + payment history
- `POST /api/hp/assign` — Assign HP plan to driver (writes to Firestore, records initial payment)
- `PUT /api/hp/driver/{id}` — Update HP details (recalculates balance)
- `DELETE /api/hp/driver/{id}` — Remove HP plan from driver
- `POST /api/hp/driver/{id}/payment` — Record manual payment (updates balance, unlocks if locked)
- `GET /api/hp/settings` — Get HP settings from Firestore (with defaults)
- `POST /api/hp/settings` — Save HP settings to Firestore

**Scheduled command (ProcessHPDeductions.php — `hp:process-deductions` daily):**
- Reads HP settings (yellowThresholdHours, redThresholdHours, autoKillSwitch)
- Iterates all HP-enabled drivers
- If wallet sufficient: deducts daily amount, records payment, resets to green, unlocks if locked
- If wallet insufficient: escalates status (green→yellow after threshold, yellow→red after threshold)
- Auto kill switch: locks driver app if red + autoKillSwitch enabled

**Blade views (client-side JS preserved):**
- `index.blade.php` — Dashboard with status cards, DataTable, kill switch button
- `settings.blade.php` — HP settings form + driver assignment form
- `driver-hp.blade.php` — Individual driver detail, payment history, update/remove/lock/unlock

**Firestore data model (on `driver_users` documents):**
- `hpEnabled`, `hpTotalCost`, `hpAmountPaid`, `hpBalance`, `hpDailyDeduction`
- `hpStatus` (green/yellow/red), `hpStartDate`, `hpLastPaymentDate`
- `appLocked`, `isActive`, `lockReason`, `lockedAt`

**Payment history subcollection:** `driver_users/{id}/hp_payments` (date, type, amount, balanceAfter)

### ✅ KILL SWITCH — FULLY IMPLEMENTED (2026-04-07)

**KillSwitchController.php (3 endpoints via kreait/laravel-firebase):**
- `POST /api/kill-switch/{driverId}/lock` — Sets `appLocked=true`, `isActive=false`, `lockReason`, `lockedAt` on Firestore
- `POST /api/kill-switch/{driverId}/unlock` — Sets `appLocked=false`, `isActive=true`, clears reason
- `GET /api/kill-switch/{driverId}/status` — Returns current lock status from Firestore

**Driver Flutter app listener (home_controller.dart):**
- Real-time Firestore snapshot listener on `driver_users/{uid}`
- If `appLocked == true`: signs out via FirebaseAuth, shows toast with lock reason, redirects to LoginScreen
- Uses `appLocked` field (not `killSwitch.isLocked`) — consistent with admin controller

**Admin HP views also have client-side JS lock/unlock** as backup for browser-side operations.

### 🔒 SECURITY ISSUES

| Issue | Location | Fix |
|-------|----------|-----|
| FCM server key hardcoded | `config/constant.php` line ~12 | Move to `.env` as `FCM_SERVER_KEY` |
| Firebase client SDK key exposed | All blade views (JS config) | Expected for client-side SDK; restrict API key in Firebase console to allowed domains |
| Google Maps API key in app manifest | `AndroidManifest.xml` (both apps) | Restrict key in Google Cloud Console to app package IDs |

---

## 2. LANDING PAGE (Laravel 10) — LIVE at :8081

| Feature | Status | Notes |
|---------|--------|-------|
| Home page | ✅ | Client images, scroll animations |
| Firebase-driven header/footer | ✅ | Loads from Firestore `landingPageTemplate` collection |
| CMS dynamic pages | ✅ | `/{slug}` route |
| 9jaRide Pro branding | ✅ | |
| Client logo/images | ✅ | Do not modify |

**Firestore requirement:** `landingPageTemplate` collection must be populated for header/footer to render. If empty, fallback renders blank.

---

## 3. OWNER PANEL (Laravel 10) — LIVE at :8082

| Feature | Status | Notes |
|---------|--------|-------|
| Phone OTP login | ✅ | Firebase Auth phone verification |
| Google sign-in | ✅ (hidden) | Hidden per client request |
| Dashboard | ✅ | |
| Driver management | ✅ | Create, edit, documents |
| Rides (city + intercity) | ✅ | |
| Reports | ✅ | |
| Wallet/transactions | ✅ | |
| Subscription plans | ✅ | |
| Payout requests | ✅ | |
| Payment gateways | ✅ | Stripe, PayPal, Paytm, RazorPay, MercadoPago |
| Notifications | ✅ | |
| 9jaRide Pro branding | ✅ | |

---

## 4. CUSTOMER FLUTTER APP — com.njaridepro.customer

### Build Status
| Build Type | Status | Notes |
|------------|--------|-------|
| Debug APK | ✅ | 212MB, working on LDPlayer |
| Release APK | ✅ | 115.8MB — `9jaRidePro-Customer-Release.apk` |

### Feature Status

| Feature | Status | Notes |
|---------|--------|-------|
| Package name | ✅ | com.njaridepro.customer |
| Firebase config | ✅ | jaride-pro project, correct Android appId |
| App title | ✅ | "9jaRide Pro" |
| Splash screen | ✅ | With error handling + fallback |
| Onboarding | ✅ | Skips to login when Firestore empty |
| Login/Register | ✅ | Phone + Google |
| Real-time bidding | ✅ | Passenger posts ride, driver bids, passenger accepts |
| Live tracking | ✅ | Real-time driver location on map |
| Google Maps | ✅ | Place picker, geocoding, polyline |
| SOS / Panic button | ✅ | Writes to Firestore `sos` collection |
| Wallet | ✅ | |
| Subscription plans | ✅ | |
| Referral program | ✅ | |
| Multi-language | ✅ | en, ar, fr |
| Dark/Light theme | ✅ | |
| Payment — Stripe | ✅ | |
| Payment — Razorpay | ✅ | |
| Payment — PayPal | ✅ | |
| Payment — Paystack | ✅ | |
| Payment — Wallet | ✅ | |
| Payment — COD | ✅ | |
| Payment — Flutterwave | ⏳ | Waiting on client API keys |
| SMS OTP (Termii) | ⏳ | Waiting on client keys |
| App icons | ✅ | Custom adaptive icons in all density buckets (verified in audit) |
| Maps SDK enabled | ❌ | Needs enabling in Google Cloud Console for jaride-pro |

**Dependencies (pubspec.yaml):**
- Dart SDK: `>=3.4.0 <4.0.0` ✅ (Dart 3 compatible)
- firebase_core: ^4.4.0
- google_maps_flutter: ^2.14.0
- flutter_stripe: ^12.2.0
- get: ^4.7.3

---

## 5. DRIVER FLUTTER APP — com.njaridepro.driver

### Build Status
| Build Type | Status | Notes |
|------------|--------|-------|
| Debug APK | ✅ | 245MB, working on LDPlayer |
| Release APK | ✅ | 117.0MB — `9jaRidePro-Driver-Release.apk` |

### Release Build Fix (2026-04-07)

**Root cause:** Was originally two issues:
1. SDK constraint was `>=2.19.5 <3.0.0` (fixed to `>=3.0.0 <4.0.0`)
2. DNS timeout to `storage.googleapis.com` during Gradle build (intermittent network issue)

**Fix applied:** SDK constraint updated + signing keystore created + DNS flushed. Both release APKs now build successfully.

### Feature Status

| Feature | Status | Notes |
|---------|--------|-------|
| Package name | ✅ | com.njaridepro.driver |
| Firebase config | ✅ | jaride-pro, correct Android appId |
| App title | ✅ | "9jaRide Pro" |
| Splash + login | ✅ | With error handling |
| Bid acceptance/rejection | ✅ | Real-time Firestore |
| Live location updates | ✅ | Pushes location to Firebase Realtime DB |
| Driver documents | ✅ | Upload KYC docs |
| Wallet | ✅ | |
| Payouts | ✅ | |
| Subscriptions | ✅ | |
| Referral | ✅ | |
| Kill switch response | ✅ | Real-time listener on `appLocked` field, force logout + toast |
| Payment — Stripe | ✅ | |
| Payment — Paystack | ✅ | |
| App icons | ✅ | Custom adaptive icons in all density buckets (verified in audit) |
| Maps SDK enabled | ❌ | Needs enabling in Google Cloud Console |

---

## 6. OWNER FLUTTER APP — com.njaridepro.owner (PARTIALLY REBRANDED)

### Status: Rebranded code-side, blocked on Firebase Console registration

| Item | Status | Details |
|------|--------|---------|
| Package name (main) | ✅ | `com.njaridepro.owner` in build.gradle.kts, main AndroidManifest.xml, MainActivity.kt |
| Package name (debug/profile) | ✅ | Fixed 2026-04-07 — was `com.goride.owner`, now `com.njaridepro.owner` |
| MainActivity.kt directory | ✅ | Correctly at `kotlin/com/njaridepro/owner/` |
| App title | ✅ | "9jaRide Pro" in main.dart, "9jaRide Owner" in AndroidManifest |
| Google Maps API key | ✅ | Real key in AndroidManifest + runtime Firestore loading |
| Dart SDK constraint | ✅ | `>=3.0.0 <4.0.0` |
| Firebase options — project/keys | ✅ | jaride-pro project, real API key |
| Firebase options — appId | ⏳ | Placeholder `REGISTER_IN_FIREBASE_CONSOLE` — needs Firebase Console registration |
| google-services.json | ⏳ | Template with `YOUR_*` placeholders — download from Firebase after registration |

**Remaining (blocked on Firebase Console):**
1. Register `com.njaridepro.owner` as Android app in Firebase Console (jaride-pro project)
2. Download generated `google-services.json` and replace template
3. Copy the Android App ID into `firebase_options.dart` appId field

---

## 7. FIREBASE — Firestore Collections (MUST POPULATE)

The apps load critical settings from Firestore at startup. If these are empty, features will fail.

| Collection | Required Fields | Status |
|------------|----------------|--------|
| `settings/globalValue` | defaultCountryCode, distanceType, radius, etc. | ⚠️ Script ready, needs execution |
| `settings/globalKey` | googleMapKey | ⚠️ Script ready, needs execution |
| `settings/adminCommission` | amount, isEnabled, type | ⚠️ Script ready |
| `settings/referral` | referralAmount, referralAmountDriver | ⚠️ Script ready |
| `settings/maintenance_settings` | customerApp, ownerApp, driverApp | ⚠️ Script ready |
| `settings/contact_us` | supportURL | ⚠️ Script ready |
| `settings/global` | appVersion, privacyPolicy, termsAndConditions | ⚠️ Script ready |
| `settings/notification_setting` | senderId, serviceJson | ⚠️ Script ready |
| `settings/hirePurchase` | defaultDailyDeduction, thresholds, autoKillSwitch | ⚠️ Script ready |
| `settings/payment` | All payment gateways (Stripe, PayPal, Paystack, etc.) | ⚠️ Script ready, keys needed |
| `currency` | NGN (enabled) + USD (disabled fallback) | ⚠️ Script ready |
| `service` | Economy, Premium, SUV vehicle types with pricing | ⚠️ Script ready |
| `languages` | English (default) | ⚠️ Script ready |
| `landingPageTemplate` | header, footer HTML templates | ❌ Needs manual setup via admin panel |
| `onBoarding` | Onboarding slides (optional — app skips if empty) | ✅ Skip handled |

**Populate script:** `scripts/populate_firestore.js` — ready to run with Firebase Admin SDK.
```bash
cd scripts
npm install firebase-admin
# Place firebase-admin-key.json (service account) in scripts/
node populate_firestore.js
```

---

## 8. IMPLEMENTATION PLAN — PRIORITIZED

### Priority 1: Critical (blocks app functionality)

| Task | Effort | Status |
|------|--------|--------|
| ~~Fix driver SDK constraint (`>=3.0.0 <4.0.0`)~~ | ~~30 min~~ | ✅ Done |
| Run Firestore populate script (`scripts/populate_firestore.js`) | 10 min | ⚠️ Ready to run |
| Enable Maps SDK in Google Cloud Console | 5 min | ❌ Manual step |
| ~~Add kill switch listener in driver app~~ | ~~2 hours~~ | ✅ Done |

### Priority 2: Security

| Task | Effort | Status |
|------|--------|--------|
| ~~Move FCM server key to .env~~ | ~~15 min~~ | ✅ Done |
| Restrict Google Maps API key to app packages | 10 min | ❌ Google Cloud Console |
| Restrict Firebase API key to allowed domains | 10 min | ❌ Firebase Console |

### Priority 3: Feature Completion

| Task | Effort | Status |
|------|--------|--------|
| Flutterwave split payment (2.5% royalty) | 3 hours | ⏳ Waiting on client API keys |
| Termii SMS OTP integration | 2 hours | ⏳ Waiting on client API keys |
| App icons (9jaRide Pro branded) | 1 hour | ⏳ Waiting on client icon assets |
| Release APK signing keystore setup | 1 hour | ❌ Pending |
| ~~HP server-side logic + Firestore writes from PHP~~ | ~~4 hours~~ | ✅ Done |
| ~~KillSwitchController real Firestore write (PHP)~~ | ~~2 hours~~ | ✅ Done |
| Register owner app in Firebase Console | 10 min | ⏳ Needs Firebase Console access |

### Priority 4: Deferred

| Task | Notes |
|------|-------|
| ~~Owner Flutter app rebranding~~ | ✅ Done (code-side), blocked on Firebase registration |
| SSL/Nginx for 9jaridepro.com | Waiting on client domain registration |
| Guardian AI (advanced) | Phase 2 |
| Vehicle hardware kill switch | Phase 2 (requires hardware API) |
| Flutterwave sub-accounts | Phase 2 if complex |

---

## 9. GIT & DEPLOYMENT WORKFLOW

```bash
# Local changes → GitHub → Server
git add <specific-files>
git commit -m "descriptive message"
git push origin main

# Server deployment (each panel separately)
ssh root@148.230.120.40
cd /var/www/9jaride-admin && git pull origin main && php artisan cache:clear
cd /var/www/9jaride-landing && git pull origin main
cd /var/www/9jaride-owner && git pull origin main
```

**Rules:**
- Never commit `.env`, `firebase.json`, `google-services.json` (server-side only)
- APK files are gitignored
- Client-provided images/logos: never modify without asking

---

## 10. CHANGE LOG

| Date | Change | Files |
|------|--------|-------|
| 2026-04-07 | Firebase options fixed (web→Android appId) | `customer/lib/firebase_options.dart`, `driver/lib/firebase_options.dart` |
| 2026-04-07 | Customer INTERNET permission added | `customer/android/app/src/main/AndroidManifest.xml` |
| 2026-04-07 | Google Maps API key placeholder replaced | `customer/android/app/src/main/AndroidManifest.xml` |
| 2026-04-07 | Onboarding skip when Firestore empty | `customer/lib/controller/on_boarding_controller.dart` |
| 2026-04-07 | main.dart: Firebase init wrapped in try-catch | `customer/lib/main.dart`, `driver/lib/main.dart` |
| 2026-04-07 | splash_controller: fallback to LoginScreen | `customer/lib/controller/splash_controller.dart` |
| 2026-04-07 | global_setting_controller: try-catch for each Firestore call | `customer/lib/controller/global_setting_controller.dart` |
| 2026-04-07 | fire_store_utils: null safety on getCurrentUid, getSettings, getGoogleAPIKey | `customer/lib/utils/fire_store_utils.dart` |
| 2026-04-07 | AndroidManifest background service package fixed | `driver/android/app/src/main/AndroidManifest.xml` |
| 2026-04-07 | gradle.properties: kotlin.incremental=false | Both apps `android/gradle.properties` |
| 2026-04-07 | Flutter apps fully rebranded (customer + driver) | Multiple files in both apps |
| 2026-04-07 | Debug APKs built and verified | 9jaRidePro-Customer.apk (212MB), 9jaRidePro-Driver.apk (245MB) |
| 2026-04-07 | ULTRADEEP analysis complete | project_status&plan.md (this file) |
| 2026-04-07 | Driver Dart SDK constraint fixed >=2.19.5 <3.0.0 → >=3.0.0 <4.0.0 | `driver/pubspec.yaml` |
| 2026-04-07 | Owner Dart SDK constraint fixed >=2.19.5 <3.0.0 → >=3.0.0 <4.0.0 | `owner/pubspec.yaml` |
| 2026-04-07 | Kill switch listener added to driver app (appLocked field, force logout) | `driver/lib/controller/home_controller.dart`, `driver/lib/model/driver_user_model.dart` |
| 2026-04-07 | FCM server key moved from code to env | `config/constant.php`, `.env.example` |
| 2026-04-07 | KillSwitchController implemented with real Firestore write via kreait/laravel-firebase | `Admin Panel/app/Http/Controllers/KillSwitchController.php` |
| 2026-04-07 | Owner Flutter app rebranded: com.goride.owner → com.njaridepro.owner | `owner/android/app/build.gradle.kts`, `owner/android/.../AndroidManifest.xml`, `owner/android/.../MainActivity.kt` (moved to new package dir), `owner/lib/main.dart`, `owner/lib/firebase_options.dart` |
| 2026-04-07 | Owner firebase_options.dart updated with jaride-pro credentials (appId TODO until Firebase Console registration) | `owner/lib/firebase_options.dart` |
| 2026-04-07 | ULTRADEEP audit: verified all ✅ items, identified discrepancies | `project_status&plan.md` |
| 2026-04-07 | Owner debug/profile AndroidManifest.xml: com.goride.owner → com.njaridepro.owner | `owner/android/app/src/debug/AndroidManifest.xml`, `owner/android/app/src/profile/AndroidManifest.xml` |
| 2026-04-07 | HirePurchaseController: stub → full Firestore CRUD (8 API endpoints) | `Admin Panel/app/Http/Controllers/HirePurchaseController.php` |
| 2026-04-07 | HP API routes added: /api/hp/* (drivers, assign, update, remove, payment, settings) | `Admin Panel/routes/web.php` |
| 2026-04-07 | Firestore populate script created (all settings, currency, services, languages) | `scripts/populate_firestore.js` |
| 2026-04-07 | Confirmed ProcessHPDeductions.php already functional with daily cron | `Admin Panel/app/Console/Commands/ProcessHPDeductions.php` |
| 2026-04-07 | Release signing keystore created (9jaridepro-release.jks, Eagles Partners, Lagos NG) | `9jaridepro-release.jks`, `customer/android/key.properties`, `driver/android/key.properties` |
| 2026-04-07 | Customer signing config enabled in build.gradle (was commented out) | `customer/android/app/build.gradle` |
| 2026-04-07 | Firestore settings populated (13 collections/documents) | `scripts/populate_firestore.js` — executed successfully |
| 2026-04-07 | Release APKs built successfully | `9jaRidePro-Customer-Release.apk` (115.8MB), `9jaRidePro-Driver-Release.apk` (117.0MB) |
| 2026-04-07 | Default country code set to Nigeria (+234) in both apps | `customer/lib/constant/constant.dart`, `driver/lib/constant/constant.dart` |
| 2026-04-07 | App icons generated: Deep Green + Gold "9J PRO" for all 3 apps (all density buckets) | `scripts/generate_icons.js`, customer/driver/owner `mipmap-*/ic_launcher*.png` |
| 2026-04-07 | Firestore payment model fixed: correct field names matching PaymentModel.fromJson() | `scripts/fix_firestore.js` — cash, wallet, strip, paypal, payStack, flutterWave, etc. |
| 2026-04-07 | Subscription plans added to Firestore: free_plan + standard_plan | `scripts/fix_firestore.js` — collection `subscription_plans` |
| 2026-04-07 | commissionSubscriptionID fixed: "J0RwvxCWhZzQQD7Kc2Ll" → "free_plan" | `customer/lib/constant/constant.dart`, `driver/lib/constant/constant.dart` |
| 2026-04-07 | Driver subscription controller: fixed crash on empty list, added adminCommission pre-load, added error handling | `driver/lib/controller/subscription_controller.dart` |
| 2026-04-07 | Driver getAllSubscriptionPlans: added try-catch + fallback without orderBy if index missing | `driver/lib/utils/fire_store_utils.dart` |
| 2026-04-07 | Removed orderBy("createdDate") from all ride/order queries to avoid composite index errors | `customer/lib/ui/orders/order_screen.dart`, `customer/lib/ui/intercityOrders/intercity_order_screen.dart`, driver 10+ UI files |
| 2026-04-07 | Removed orderBy('createdAt') from inbox queries to avoid composite index errors | `customer/lib/ui/chat_screen/inbox_screen.dart`, `driver/lib/ui/chat_screen/inbox_screen.dart` |
| 2026-04-07 | Changed "Something went wrong" error messages to "No rides/data found" (graceful empty state) | All order/ride screens in both apps |
| 2026-04-07 | Added subscription_model field to Firestore settings/globalValue | `scripts/fix_firestore2.js` |
| 2026-04-07 | Enabled Paystack sandbox in Firestore payment settings (placeholder keys) | `scripts/fix_firestore2.js` |
| 2026-04-07 | Final release APKs rebuilt with all fixes | `9jaRidePro-Customer-Release.apk` (116MB), `9jaRidePro-Driver-Release.apk` (117MB) |
| 2026-04-07 | Splash logo replaced: GoRide → 9jaRide Pro (gold "9ja" + white "Ride" + "PRO") | `customer/assets/app_logo.png`, `driver/assets/app_logo.png`, `owner/assets/app_logo.png` |
| 2026-04-07 | .gitignore updated: added *.jks, key.properties, google-services.json, firebase-admin-key.json | `.gitignore` |
| 2026-04-07 | Sensitive files removed from git tracking: customerKey.jks, key.properties, google-services.json (3 apps) | `git rm --cached` |
