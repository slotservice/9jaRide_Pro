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
| Hire Purchase module | ⚠️ | STUB — see details below |
| Kill Switch API | ⚠️ | STUB — see details below |
| Settings (global, business, payment, landing template) | ✅ | |
| Firebase integration | ✅ | Client-side JS SDK in blade views |
| Branding (9jaRide Pro) | ✅ | Deep Green + Gold theme |

### ⚠️ HIRE PURCHASE — STUB (needs real implementation)

**Current state:**
- `HirePurchaseController.php` — 3 methods, each just returns a view. Zero business logic.
- All HP logic is client-side JavaScript in `hire-purchase/index.blade.php` querying Firestore directly.
- No MySQL model or migration for HP records.
- No server-side deduction calculation or cron.

**What works (client-side only):**
- HP index view renders Green/Yellow/Red status cards (counts loaded from Firestore JS).
- Kill switch activation in HP view writes to Firestore via browser JS (`drivers/{id}` document `killSwitch.isLocked = true`).
- HP settings view renders form (fields not saved server-side).
- Driver HP detail view loads driver's HP schedule from Firestore.

**What is missing:**
- Server-side HP data model (daily deduction logic, overdue calculation).
- Cron/scheduled job for automatic status updates (Green→Yellow after 24h, Yellow→Red after 48h).
- HP assignment form that saves to Firestore AND database.
- HP settings that persist across restarts.

**Fix plan:**
```
1. Create Firestore collection "hirePurchase" schema:
   - driverId, vehicleId, totalAmount, weeklyDeduction, startDate,
     amountPaid, balance, status (green/yellow/red), lastPaymentDate
2. Update HirePurchaseController to read/write this via Firebase Admin SDK
3. Add scheduled command for daily status check
```

### ⚠️ KILL SWITCH — STUB (works via client-side JS, not API)

**Current state:**
- `KillSwitchController.php` — 3 endpoints (lock/unlock/status) that return JSON stubs.
- No actual Firestore write in the controller.
- The actual kill switch write happens in `hire-purchase/index.blade.php` JS:
  ```javascript
  database.collection('drivers').doc(driverId).update({
      'killSwitch.isLocked': true,
      'killSwitch.reason': reason,
      'killSwitch.lockedAt': firebase.firestore.FieldValue.serverTimestamp()
  })
  ```
- Driver app must listen to `killSwitch.isLocked` field and lock itself.

**What works:**
- Admin can click "Lock" in HP view → Firestore `drivers/{id}.killSwitch.isLocked = true` is set.
- The API endpoint exists but is decorative (no Firestore write).

**What is missing:**
- Driver Flutter app must check `killSwitch.isLocked` on startup and during session.
- API endpoint should write to Firestore via Firebase Admin SDK (not rely on browser).
- Kill switch status should show in admin driver list.

**Fix plan:**
```
1. Install kreait/laravel-firebase package (or use HTTP API) to write to Firestore from PHP.
2. KillSwitchController::lock() → Firestore drivers/{id} set killSwitch.isLocked=true.
3. Driver app: add StreamBuilder on drivers/{uid} to listen and force-logout if isLocked.
```

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
| Release APK | ❌ | Dart AOT snapshot failure (unrelated to this app's SDK — see driver) |

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
| App icons | ❌ | Still GoRide default icons |
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
| Release APK | 🐛 | "Dart snapshot generator failed with exit code 1" |

### 🐛 ROOT CAUSE OF RELEASE BUILD FAILURE

**Problem:** `pubspec.yaml` environment SDK constraint is `>=2.19.5 <3.0.0`.  
**Installed Flutter uses Dart 3.x** (comes with Flutter 3.x+).  
**AOT compilation (release only) strictly enforces SDK constraints.**  
**Debug builds are more lenient; release AOT fails the constraint.**

**Fix:**
```yaml
# In GoRide_V5.6_Source_Code/Applications/GoRide-5.6/driver/pubspec.yaml
environment:
  sdk: '>=3.0.0 <4.0.0'   # was: '>=2.19.5 <3.0.0'
```
Then run:
```bash
cd "GoRide_V5.6_Source_Code/Applications/GoRide-5.6/driver"
flutter clean
flutter pub upgrade
flutter build apk --release
```
**Note:** Some package APIs may have changed between Dart 2.x and 3.x. After upgrade, check for breaking changes (especially null-safety and removed APIs).

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
| Kill switch response | ⚠️ | No listener for `killSwitch.isLocked` field in driver app |
| Payment — Stripe | ✅ | |
| Payment — Paystack | ✅ | |
| App icons | ❌ | Still GoRide default icons |
| Maps SDK enabled | ❌ | Needs enabling in Google Cloud Console |

---

## 6. OWNER FLUTTER APP — com.goride.owner ⚠️ NOT REBRANDED

### Status: INCOMPLETE — Low priority (client hasn't requested this yet)

| Issue | Details |
|-------|---------|
| Package name | ❌ Still `com.goride.owner` — needs 7 files updated |
| App title | ❌ Still "GoRide" in main.dart |
| Firebase config | ❌ All placeholder values (YOUR_ANDROID_API_KEY, etc.) |
| Android label | ❌ Generic "owner" label |
| Google Maps API key | ❌ Placeholder `YOUR_API_KEY_HERE` |

**Files to update when rebranding:**
1. `android/app/build.gradle.kts` — namespace + applicationId
2. `android/app/src/main/kotlin/.../MainActivity.kt` — package + directory rename
3. `android/app/src/main/AndroidManifest.xml` — package, API key
4. `ios/Runner.xcodeproj/project.pbxproj` — bundle identifier
5. `lib/firebase_options.dart` — all Firebase credentials
6. `android/app/google-services.json` — add package to Firebase project first
7. `lib/main.dart` — app title

---

## 7. FIREBASE — Firestore Collections (MUST POPULATE)

The apps load critical settings from Firestore at startup. If these are empty, features will fail.

| Collection | Required Fields | Status |
|------------|----------------|--------|
| `settings/globalValue` | currency, currencySymbol, basePrice, perKmRate, etc. | ❌ Not populated |
| `settings/globalKey` | googleMapKey, stripePublishableKey, etc. | ❌ Not populated |
| `settings/sendbird` | appId, token | ❌ Not populated |
| `settings/payfast` | merchant_id, merchant_key, passphrase | ❌ Not populated |
| `currency` | Array of currency docs with `enable: true` | ❌ Not populated |
| `services` | Vehicle types (economy, premium, etc.) | ❌ Not populated |
| `landingPageTemplate` | header, footer HTML templates | ❌ Not populated |
| `onBoarding` | Onboarding slides (optional — app skips if empty) | ✅ Skip handled |

**Populate script location:** Create `scripts/populate_firestore.js` using Firebase Admin SDK.

---

## 8. IMPLEMENTATION PLAN — PRIORITIZED

### Priority 1: Critical (blocks app functionality)

| Task | Effort | Why Critical |
|------|--------|-------------|
| Fix driver SDK constraint (`>=3.0.0 <4.0.0`) | 30 min | Release APK won't build |
| Populate Firestore settings collections | 1 hour | Apps crash/malfunction without settings |
| Enable Maps SDK in Google Cloud Console | 5 min | Maps won't render in apps |
| Add kill switch listener in driver app | 2 hours | Kill switch feature non-functional from app side |

### Priority 2: Security

| Task | Effort |
|------|--------|
| Move FCM server key to .env | 15 min |
| Restrict Google Maps API key to app packages | 10 min |
| Restrict Firebase API key to allowed domains | 10 min |

### Priority 3: Feature Completion

| Task | Effort | Blocker |
|------|--------|---------|
| Flutterwave split payment (2.5% royalty) | 3 hours | Waiting on client API keys |
| Termii SMS OTP integration | 2 hours | Waiting on client API keys |
| App icons (9jaRide Pro branded) | 1 hour | Waiting on client icon assets |
| Release APK signing keystore setup | 1 hour | — |
| HP server-side logic + Firestore writes from PHP | 4 hours | kreait/laravel-firebase package |
| KillSwitchController real Firestore write (PHP) | 2 hours | kreait/laravel-firebase package |

### Priority 4: Deferred

| Task | Notes |
|------|-------|
| Owner Flutter app rebranding | Client hasn't requested yet |
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
