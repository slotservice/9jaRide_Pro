# 9jaRide Pro — Session Handoff

**Last session ended:** 2026-04-07
**Status:** Phase 1 — APKs built and ready for client delivery
**Next session goal:** Continue testing, bug fixes, integrating client API keys when received

---

## Project Snapshot

| Item | Value |
|------|-------|
| Project | 9jaRide Pro — Nigerian taxi bidding app |
| Client | Eagles Partners (Oladoyin) |
| Budget | $2,200 (Phase 1) |
| Developer | Cindy (Windows env) |
| Project root | `d:\Freelancer-Project\Oladoyin` |
| Git repo | https://github.com/slotservice/9jaRide_Pro |
| Today's date | 2026-04-07 |

---

## Live Server Credentials

| Component | Value |
|-----------|-------|
| **VPS IP** | 148.230.120.40 |
| **OS** | Ubuntu 22.04 |
| **SSH** | root, key auth |
| **Admin Panel URL** | http://148.230.120.40 |
| **Admin login** | admin@9jaridepro.com / 9jaRide@2026! |
| **Landing URL** | http://148.230.120.40:8081 |
| **Owner Panel URL** | http://148.230.120.40:8082 (Phone OTP) |
| **MySQL User** | njaride_user / N9jaR1de_Pr0_2026! |
| **Admin DB** | njaride_admin |
| **Owner DB** | njaride_owner |
| **Flutter (local)** | C:\flutter (3.41.6, Dart 3.11.4) |
| **Flutter (VPS)** | /opt/flutter |
| **Android SDK** | C:\android-sdk |
| **Java** | 17 |

---

## Firebase Credentials

| Item | Value |
|------|-------|
| **Project ID** | jaride-pro |
| **Plan** | Spark (free) |
| **Console** | https://console.firebase.google.com → jaride-pro |
| **Database URL** | https://jaride-pro-default-rtdb.firebaseio.com |
| **Storage Bucket** | jaride-pro.firebasestorage.app |
| **Messaging Sender ID** | 872495882111 |
| **Android API Key** | AIzaSyCWXkyRu5fIX-wxCy1E9_Nx0FqnVvKvKL0 |
| **iOS API Key (customer)** | AIzaSyAHhhjNYv11qdDr6UzlW9fr_Jmd5zC_4sU |
| **Service Account Email** | firebase-adminsdk-fbsvc@jaride-pro.iam.gserviceaccount.com |
| **Service Account Key** | `scripts/firebase-admin-key.json` (DO NOT COMMIT) |

### Firebase Test Phone Numbers (set in Firebase Console)
| Phone Number | OTP Code |
|---|---|
| +2341234567890 | 123456 |
| +2340987654321 | 123456 |

---

## App Package IDs

| App | Package ID | Firebase App ID |
|-----|-----------|-----------------|
| Customer | `com.njaridepro.customer` | `1:872495882111:android:59a0787e8e28f5d99e044a` |
| Driver | `com.njaridepro.driver` | `1:872495882111:android:3d3a4334bd8fe7fa9e044a` |
| Owner | `com.njaridepro.owner` | NOT REGISTERED — placeholder appId |

---

## Signing Keystore

| Item | Value |
|------|-------|
| **File** | `9jaridepro-release.jks` (project root, gitignored) |
| **Alias** | 9jaridepro |
| **Password** | 9jaRidePr0_2026 |
| **Key password** | 9jaRidePr0_2026 |
| **DName** | CN=9jaRide Pro, OU=Mobile, O=Eagles Partners, L=Lagos, ST=Lagos, C=NG |
| **Validity** | 10000 days |
| **key.properties files** | `customer/android/key.properties`, `driver/android/key.properties` (gitignored) |

---

## Brand Identity

- **Primary color:** Deep Green `#1B5E20`
- **Accent color:** Gold `#D4AF37`
- **App icon:** "9J PRO" on deep green background, gold + white text
- **Splash logo:** "9jaRide" with gold "9ja" + white "Ride" + location pin + "PRO" subtitle

---

## What's Complete

### Admin/Landing/Owner Panels (Laravel 10 — LIVE)
- Full RBAC with Spatie permissions (150+ permissions)
- Driver/User/Owner management with KYC
- Ride management (city + intercity)
- HP module with 8 API endpoints (`/api/hp/*`)
- Kill Switch API with 3 endpoints (`/api/kill-switch/{id}/*`)
- Real Firestore writes via `kreait/laravel-firebase` package
- Daily cron `hp:process-deductions` for auto HP processing
- 9jaRide Pro branding (Deep Green + Gold theme)

### Customer Flutter App
- Package: `com.njaridepro.customer`
- Splash → Login (+234 default) → OTP → Dashboard
- Real-time bidding, live tracking, SOS, Wallet, Refer & Earn
- Dark/Light theme, multi-language (en, ar, fr)
- 9jaRide Pro icon, splash logo, branding throughout
- Cash + Wallet payments enabled
- Paystack/Flutterwave/Stripe/PayPal/etc. ready (need API keys)

### Driver Flutter App
- Package: `com.njaridepro.driver`
- Splash → Login → Subscription Plan (Free/Standard) → Dashboard
- Bidding, location updates to Realtime DB, kill switch listener
- Real-time `appLocked` listener with force-logout
- 9jaRide Pro branding

### Owner Flutter App
- Package: `com.njaridepro.owner` (rebranded but Firebase registration pending)
- Code rebranded, debug/profile manifests fixed
- Blocked: needs Firebase Console registration for `google-services.json` + appId

### APKs (Final, ready to ship)
Located at `d:\Freelancer-Project\Oladoyin\`:
| File | For | Size |
|---|---|---|
| `9jaRidePro-Customer.apk` | Send to client (arm64) | 59MB |
| `9jaRidePro-Driver.apk` | Send to client (arm64) | 61MB |
| `9jaRidePro-Customer-x86.apk` | Local LDPlayer testing | 62MB |
| `9jaRidePro-Driver-x86.apk` | Local LDPlayer testing | 63MB |

### Firestore Populated (via scripts)
All 13+ collections populated. Settings, services (3 vehicle types), currency (NGN+USD), subscription plans (Free + Standard), zones (Lagos, Abuja, Port Harcourt), languages, payment gateways, HP defaults, etc.

### Maps APIs Enabled
On `9jaride-pro` Google Cloud project:
- Maps SDK for Android
- Places API
- Directions API
- Geocoding API
- Distance Matrix API

---

## Crash Fixes Applied (Today's Session)

| Issue | Fix |
|---|---|
| Currency model null crash | `currencyModel?` with `?? 2`, `?? '₦'` fallbacks |
| `prices.first` empty list crashes (95 occurrences) | Added `firstPrice` getter to ServiceModel returning default Price() |
| Stripe null key crash | Wrapped `Stripe.publishableKey = ...` in null check |
| orderBy composite index errors | Removed `orderBy()` from wallet/withdrawal/subscription/order queries |
| Contact Us null crash | Added try-catch + `?? ""` defaults |
| `value.docs.first.data()` empty crash | Added `isNotEmpty` checks |
| Driver subscription `.first` crash | Added `subscriptionPlanList.isNotEmpty` check |
| "Something went wrong" errors | Changed to graceful "No rides found" empty state |
| Default country code | Set to `+234` Nigeria in both apps |
| GoRide splash logo | Replaced with 9jaRide Pro branded logo |
| GoRide app icons | Replaced with custom Deep Green + Gold "9J PRO" icons |
| Owner debug/profile manifests | `com.goride.owner` → `com.njaridepro.owner` |
| HirePurchaseController stub | Implemented full Firestore CRUD (8 endpoints) |
| .gitignore sensitive files | Added *.jks, key.properties, google-services.json, firebase-admin-key.json |

---

## Pending — Waiting on Client

| Item | Why blocked |
|---|---|
| **Flutterwave API keys** | Split payments (2.5% royalty) — need public + secret + encryption keys |
| **Paystack API keys** | Topup will fail with "Something went wrong" until real keys provided |
| **Termii API key** | SMS OTP for production users |
| **Domain (9jaridepro.com)** | SSL/Nginx setup |
| **Vehicle type images** | Currently shows gray placeholder — need Economy/Premium/SUV photos |
| **Owner app Firebase registration** | Needed if/when owner app deploys |

---

## Pending — Technical Tasks

- Production: set `APP_DEBUG=false` + `APP_ENV=production` in server `.env`
- Restrict Firebase API key by Android package ID (Firebase Console)
- Restrict Google Maps API key by package + SHA-1 (Google Cloud Console)
- Upload vehicle service images to Firebase Storage
- Push code changes to GitHub (HirePurchaseController, routes, etc.)

---

## Important Rules (User's Preferences)

1. **Never modify client-provided assets** — logos/images stay as-is unless asked
2. **Always git push/pull** — no direct file uploads to server
3. **Never commit sensitive files** — `.env`, `*.jks`, `google-services.json`, `firebase-admin-key.json`, `key.properties`
4. **Don't auto-rebuild APKs** — only rebuild when user explicitly says to (builds take 2-5 min each)
5. **Work in batches** — make all code fixes first, build once when user requests
6. **APKs are gitignored** — don't commit them

---

## File Structure

```
d:\Freelancer-Project\Oladoyin\
├── handoff.md                          ← This file
├── prompt.md                           ← For new Claude session
├── guide.md                            ← Complete project documentation
├── project_status&plan.md              ← Detailed status + full changelog
├── status.md                           ← Quick status overview
├── chat.md                             ← Client conversation history
├── .gitignore                          ← Includes *.jks, key.properties, etc.
├── 9jaridepro-release.jks              ← Signing keystore (gitignored)
├── 9jaRidePro-Customer.apk             ← For client (arm64, 59MB)
├── 9jaRidePro-Driver.apk               ← For client (arm64, 61MB)
├── 9jaRidePro-Customer-x86.apk         ← For LDPlayer (62MB)
├── 9jaRidePro-Driver-x86.apk           ← For LDPlayer (63MB)
├── scripts/
│   ├── firebase-admin-key.json         ← Firebase service account (gitignored)
│   ├── populate_firestore.js           ← Initial Firestore population
│   ├── fix_firestore.js                ← Payment model + subscription plans
│   ├── fix_firestore2.js               ← subscription_model + Paystack sandbox
│   ├── fix_zones.js                    ← Adds Lagos/Abuja/Port Harcourt zones
│   ├── generate_icons.js               ← App icon generator
│   └── generate_splash_logo.js         ← Splash screen logo generator
└── GoRide_V5.6_Source_Code/
    ├── Admin Panel - Landing Panel - Owner Panel/
    │   ├── Admin Panel/                ← Laravel — LIVE at :80
    │   ├── Landing Panel/              ← Laravel — LIVE at :8081
    │   └── Owner Panel/                ← Laravel — LIVE at :8082
    └── Applications/GoRide-5.6/
        ├── customer/                   ← Flutter (com.njaridepro.customer)
        ├── driver/                     ← Flutter (com.njaridepro.driver)
        └── owner/                      ← Flutter (com.njaridepro.owner, partially rebranded)
```

---

## Build Commands

```bash
# Customer release APK split by ABI (recommended)
cd "d:/Freelancer-Project/Oladoyin/GoRide_V5.6_Source_Code/Applications/GoRide-5.6/customer"
C:/flutter/bin/flutter clean
C:/flutter/bin/flutter pub get
C:/flutter/bin/flutter build apk --release --split-per-abi

# Driver release APK
cd "d:/Freelancer-Project/Oladoyin/GoRide_V5.6_Source_Code/Applications/GoRide-5.6/driver"
C:/flutter/bin/flutter build apk --release --split-per-abi

# Output: build/app/outputs/flutter-apk/
#   app-arm64-v8a-release.apk    ← For client phones
#   app-armeabi-v7a-release.apk  ← For older phones
#   app-x86_64-release.apk       ← For LDPlayer

# Then copy/rename:
cp ".../customer/build/app/outputs/flutter-apk/app-arm64-v8a-release.apk" "d:/Freelancer-Project/Oladoyin/9jaRidePro-Customer.apk"
cp ".../driver/build/app/outputs/flutter-apk/app-arm64-v8a-release.apk" "d:/Freelancer-Project/Oladoyin/9jaRidePro-Driver.apk"
```

---

## Memory Files (auto-loaded by Claude)

Located at `C:\Users\com\.claude\projects\d--Freelancer-Project-Oladoyin\memory\`:
- `MEMORY.md` — index
- `project_9jaride.md` — project overview
- `user_developer.md` — user profile (Cindy)
- `feedback_no_modify_client_assets.md` — never alter client logos
- `feedback_git_workflow.md` — always use git push/pull
- `feedback_no_rebuild.md` — only rebuild APKs when explicitly asked
- `project_milestones.md` — milestone breakdown ($2,200 Phase 1, no M4)

---

## Milestones

| Milestone | Scope | Amount | Status |
|---|---|---|---|
| **M1** | Admin Dashboard | $400 | PAID + DELIVERED |
| **M2** | HP Module + Kill Switch | (part of $1,800 remaining) | DELIVERED |
| **M3** | Customer + Driver APKs + full platform | (remainder) | APKs ready for client testing |

**No formal Milestone 4** — Phase 1 ends when M3 is accepted. Phase 2 mentioned but undefined ("first 100 rides will pay for Phase 2").

---

## Quick Health Check Commands

```bash
# Check git status
cd d:/Freelancer-Project/Oladoyin && git status

# Check APK sizes
ls -lh d:/Freelancer-Project/Oladoyin/*.apk

# Check Firestore (need firebase-admin-key.json in scripts/)
cd d:/Freelancer-Project/Oladoyin/scripts && node -e "
const admin = require('firebase-admin');
admin.initializeApp({ credential: admin.credential.cert(require('./firebase-admin-key.json')) });
admin.firestore().collection('settings').doc('payment').get().then(d => console.log(JSON.stringify(d.data(), null, 2).slice(0, 500)));
"
```
