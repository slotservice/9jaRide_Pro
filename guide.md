# 9jaRide Pro - Complete Project Guide

**Project:** Taxi bidding app for Nigerian market  
**Client:** Eagles Partners (Oladoyin)  
**Phase:** 1 ($2,200)  
**Stack:** Flutter (mobile) + Laravel 10 (backend) + Firebase (Firestore/Auth/Realtime DB)  
**Last Updated:** 2026-04-07

---

## Table of Contents

1. [Project Architecture](#1-project-architecture)
2. [Server Infrastructure](#2-server-infrastructure)
3. [Admin Panel](#3-admin-panel)
4. [Customer App](#4-customer-app)
5. [Driver App](#5-driver-app)
6. [Owner App](#6-owner-app)
7. [Firebase Setup](#7-firebase-setup)
8. [Build Guide](#8-build-guide)
9. [Testing Guide](#9-testing-guide)
10. [Deployment Guide](#10-deployment-guide)
11. [Client Delivery Checklist](#11-client-delivery-checklist)
12. [Remaining Work](#12-remaining-work)

---

## 1. Project Architecture

```
9jaRide Pro Platform
├── Admin Panel (Laravel 10)     → manages drivers, rides, HP, kill switch
├── Landing Page (Laravel 10)    → public website
├── Owner Panel (Laravel 10)     → fleet owner management
├── Customer App (Flutter)       → passengers book rides, bid, pay
├── Driver App (Flutter)         → drivers accept bids, navigate, earn
├── Owner App (Flutter)          → fleet owners manage drivers (not deployed yet)
└── Firebase (Firestore/Auth/RTDB)
    ├── Firestore  → settings, users, orders, payments, chat
    ├── Auth       → phone OTP + Google sign-in
    └── Realtime DB → live driver locations
```

### Key Package Names
| App | Package ID | Firebase App ID |
|-----|-----------|-----------------|
| Customer | `com.njaridepro.customer` | `1:872495882111:android:59a0787e8e28f5d99e044a` |
| Driver | `com.njaridepro.driver` | `1:872495882111:android:3d3a4334bd8fe7fa9e044a` |
| Owner | `com.njaridepro.owner` | Not registered yet |

### Brand Colors
- **Deep Green:** `#1B5E20`
- **Gold:** `#D4AF37`
- **App Icons:** "9J PRO" on deep green background

---

## 2. Server Infrastructure

| Component | Details |
|-----------|---------|
| **VPS IP** | 148.230.120.40 |
| **OS** | Ubuntu 22.04 |
| **SSH** | root, key auth |
| **Web Server** | Nginx |
| **PHP** | 8.1+ |
| **MySQL User** | njaride_user / N9jaR1de_Pr0_2026! |
| **Admin DB** | njaride_admin |
| **Owner DB** | njaride_owner |

### Live URLs
| Panel | URL | Credentials |
|-------|-----|-------------|
| Admin | http://148.230.120.40 | admin@9jaridepro.com / 9jaRide@2026! |
| Landing | http://148.230.120.40:8081 | Public |
| Owner | http://148.230.120.40:8082 | Phone OTP |

---

## 3. Admin Panel

**Location:** `GoRide_V5.6_Source_Code/Admin Panel - Landing Panel - Owner Panel/Admin Panel/`

### Key Features
- Full RBAC with Spatie permissions (150+ permissions)
- Driver/User/Owner management with KYC documents
- Ride management (city + intercity)
- Zone & Surge pricing
- GOD Eye (real-time driver map)
- **Hire Purchase module** — assign vehicle payment plans to drivers
- **Kill Switch** — remotely lock/unlock driver apps
- Payment transactions, payouts, subscriptions
- SOS incident management
- Reports, CMS, FAQs, Notifications

### Hire Purchase System
- **8 API endpoints** at `/api/hp/*` for full CRUD
- **Daily cron** `hp:process-deductions` — auto-deducts from driver wallets
- **Status progression:** Green (paid) → Yellow (24h overdue) → Red (48h overdue)
- **Auto kill switch:** Locks driver app when Red + autoKillSwitch enabled
- **Data stored in Firestore** `driver_users` collection with HP fields

### Kill Switch System
- **3 API endpoints** at `/api/kill-switch/{driverId}/*`
- Writes `appLocked=true/false` to Firestore `driver_users/{id}`
- Driver app listens in real-time and force-logouts when locked

---

## 4. Customer App

**Location:** `GoRide_V5.6_Source_Code/Applications/GoRide-5.6/customer/`  
**Package:** `com.njaridepro.customer`  
**Dart SDK:** `>=3.4.0 <4.0.0`

### Core Flow
1. Splash → Login (Phone +234 / Google) → OTP verification
2. Dashboard → "Where you want to go?" → Pick location
3. Select Vehicle (Economy/Premium/SUV) → Enter offer price
4. Select Payment (Cash / Wallet) → Book Ride
5. Real-time bidding — drivers see the ride and bid
6. Accept preferred driver → Live tracking → Complete ride → Rate

### Menu Features
| Screen | Status | Notes |
|--------|--------|-------|
| Rides (Active/Completed/Canceled) | Working | Shows ride history |
| OutStation Rides | Working | Intercity rides |
| My Wallet | Working | Balance, top-up, transactions |
| Inbox | Working | Chat with drivers |
| Refer & Earn | Working | Referral code sharing |
| Profile | Working | Edit name, photo, email |
| Contact Us | Working | Email, phone, address |
| Settings | Working | Language, dark/light theme |
| SOS/Panic | Working | Emergency button during rides |

### Payment Methods
| Method | Status | Notes |
|--------|--------|-------|
| Cash | Enabled | Works out of the box |
| Wallet | Enabled | Top-up via external gateways |
| Paystack | Ready | Needs real API keys from client |
| Flutterwave | Ready | Needs real API keys from client |
| Stripe | Ready | Needs publishable key |
| PayPal | Ready | Needs client/secret |
| Razorpay | Ready | Needs key/secret |

---

## 5. Driver App

**Location:** `GoRide_V5.6_Source_Code/Applications/GoRide-5.6/driver/`  
**Package:** `com.njaridepro.driver`  
**Dart SDK:** `>=3.0.0 <4.0.0`

### Core Flow
1. Splash → Login (Phone +234 / Google) → OTP
2. Subscription Plan selection (Free Plan available)
3. Dashboard — shows available rides in area
4. See ride request → Bid with offer price → Wait for acceptance
5. Navigate to passenger → Start ride → Navigate to destination → Complete
6. Earnings added to wallet

### Kill Switch Integration
- Real-time listener on `driver_users/{uid}.appLocked`
- If `true`: force sign-out, show reason toast, redirect to login
- Integrated with Hire Purchase overdue system

### Menu Features
| Screen | Status |
|--------|--------|
| Rides | Working |
| My Wallet | Working |
| Inbox | Working |
| Documents (KYC) | Working |
| Refer & Earn | Working |
| Profile | Working |
| Subscription Plans | Working (Free + Standard) |

---

## 6. Owner App

**Location:** `GoRide_V5.6_Source_Code/Applications/GoRide-5.6/owner/`  
**Package:** `com.njaridepro.owner`  
**Status:** Code rebranded, Firebase registration pending

### Remaining Steps
1. Register `com.njaridepro.owner` in Firebase Console
2. Download `google-services.json` and place in `android/app/`
3. Update `firebase_options.dart` with real appId
4. Build and test

---

## 7. Firebase Setup

**Project:** jaride-pro  
**Plan:** Spark (free)  
**Console:** https://console.firebase.google.com → jaride-pro

### Required Collections (populated via script)

| Collection | Purpose |
|------------|---------|
| `settings/globalValue` | App config (country, radius, map type) |
| `settings/globalKey` | Google Maps API key |
| `settings/payment` | Payment gateway configs (all gateways) |
| `settings/adminCommission` | Commission settings |
| `settings/hirePurchase` | HP defaults (deduction, thresholds) |
| `settings/referral` | Referral amounts |
| `settings/maintenance_settings` | App maintenance mode toggles |
| `settings/contact_us` | Support contact info |
| `settings/global` | App version, privacy policy, T&C |
| `currency` | NGN (enabled), USD (fallback) |
| `service` | Vehicle types: Economy, Premium, SUV |
| `subscription_plans` | Free Plan, Standard Plan |
| `languages` | English (default) |
| `users` | Customer accounts (auto-created on register) |
| `driver_users` | Driver accounts + HP fields + location |
| `orders` | City ride orders |
| `orders_intercity` | Intercity ride orders |
| `chat` | In-app messaging |
| `sos` | Emergency incidents |
| `wallet_transaction` | Payment/topup history |

### Populate Script
```bash
cd scripts/
npm install firebase-admin
# Place firebase-admin-key.json (from Firebase Console → Service Accounts)
node populate_firestore.js    # Initial population (13 collections)
node fix_firestore.js         # Payment model + subscription plans
node fix_firestore2.js        # subscription_model field + Paystack sandbox
```

### Test Phone Numbers (for Firebase Auth)
Set up in Firebase Console → Authentication → Sign-in method → Phone → Test numbers:

| Phone Number | OTP Code | Use For |
|---|---|---|
| +2341234567890 | 123456 | Customer testing |
| +2340987654321 | 123456 | Driver testing |

---

## 8. Build Guide

### Prerequisites
- Flutter SDK: 3.41+ (`C:\flutter`)
- Dart SDK: 3.11+ (comes with Flutter)
- Java: 17
- Android SDK: `C:\android-sdk`
- Node.js: 18+ (for scripts)

### Build Commands

**Debug APK (for quick testing):**
```bash
cd "GoRide_V5.6_Source_Code/Applications/GoRide-5.6/customer"
C:/flutter/bin/flutter clean
C:/flutter/bin/flutter pub get
C:/flutter/bin/flutter build apk --debug
```

**Release APK — Fat (all architectures, ~116MB):**
```bash
C:/flutter/bin/flutter build apk --release
```

**Release APK — Split by architecture (recommended):**
```bash
C:/flutter/bin/flutter build apk --release --split-per-abi
```
This produces 3 APKs:
| File | Target | Size |
|------|--------|------|
| `app-arm64-v8a-release.apk` | **Most modern phones** (send to client) | ~59MB |
| `app-armeabi-v7a-release.apk` | Older 32-bit phones | ~55MB |
| `app-x86_64-release.apk` | Emulators (LDPlayer) | ~61MB |

APK output location: `build/app/outputs/flutter-apk/`

### Signing Keystore
- **File:** `9jaridepro-release.jks` (project root — DO NOT commit)
- **Alias:** 9jaridepro
- **Password:** 9jaRidePr0_2026
- **key.properties** in each app's `android/` directory (DO NOT commit)

---

## 9. Testing Guide

### LDPlayer Testing
1. Build split APKs (`--split-per-abi`)
2. Install `app-x86_64-release.apk` on LDPlayer (drag and drop)
3. Set up test phone numbers in Firebase Console (see Section 7)

### Test Checklist - Customer App

| Test | Steps | Expected |
|------|-------|----------|
| Login | Open app → +234 flag → Enter test number → OTP 123456 | Dashboard loads |
| Book Ride | Dashboard → Enter locations → Select vehicle → Enter price → Cash → Book | "Ride Placed" status |
| Rides List | Menu → Rides → Active/Completed/Canceled tabs | Shows ride list or "No rides found" |
| OutStation | Menu → OutStation Rides | Same as above |
| Wallet | Menu → My Wallet | Shows balance (₦0.00) |
| Wallet Topup | My Wallet → Topup → Enter amount → Select Paystack | Payment flow opens |
| Inbox | Menu → Inbox | Empty or chat list |
| Refer & Earn | Menu → Refer & Earn | Shows referral code |
| Profile | Menu → Profile | Can edit name, photo |
| Contact Us | Menu → Contact Us | Shows support info |
| Settings | Menu → Settings | Language toggle, theme toggle |

### Test Checklist - Driver App

| Test | Steps | Expected |
|------|-------|----------|
| Login | Open app → +234 flag → Enter test number → OTP 123456 | Subscription screen |
| Subscription | Select "Free Plan" → Confirm | Dashboard loads |
| Dashboard | After subscription | Shows map, online/offline toggle |
| Rides | Menu → Rides | Shows ride list or empty |
| Wallet | Menu → My Wallet | Shows balance |
| Inbox | Menu → Inbox | Empty or chat list |
| Documents | Menu → Documents | KYC upload screen |
| Kill Switch | Admin locks driver from panel | App force-logouts with toast message |

---

## 10. Deployment Guide

### Server Deployment (Laravel panels)
```bash
# Local → GitHub → Server
cd d:/Freelancer-Project/Oladoyin
git add <specific-files>
git commit -m "descriptive message"
git push origin main

# SSH to server
ssh root@148.230.120.40

# Update Admin Panel
cd /var/www/9jaride-admin
git pull origin main
php artisan cache:clear
php artisan config:clear

# Update Landing Page
cd /var/www/9jaride-landing
git pull origin main

# Update Owner Panel
cd /var/www/9jaride-owner
git pull origin main
```

### Production Checklist (before going live)
| Task | Status |
|------|--------|
| Set `APP_DEBUG=false` in server `.env` | Required |
| Set `APP_ENV=production` in server `.env` | Required |
| Enable Maps SDK in Google Cloud Console | Required |
| Restrict Firebase API key to app package IDs | Required |
| Restrict Google Maps key to app package IDs | Required |
| Add Flutterwave API keys to Firestore `settings/payment` | Waiting on client |
| Add Paystack API keys to Firestore `settings/payment` | Waiting on client |
| Add Termii API key for SMS OTP | Waiting on client |
| Upload vehicle images to Firebase Storage | Needed (gray placeholder until done) |
| Register owner app in Firebase Console | When needed |
| Set up domain + SSL (9jaridepro.com) | Waiting on client |

---

## 11. Client Delivery Checklist

### APK Files to Send
| File | For | Notes |
|------|-----|-------|
| `app-arm64-v8a-release.apk` (Customer) | Client testing on phone | Rename to `9jaRidePro-Customer.apk` |
| `app-arm64-v8a-release.apk` (Driver) | Client testing on phone | Rename to `9jaRidePro-Driver.apk` |

### What to Tell Client
1. **Login:** Use Nigerian phone number (+234), OTP will be sent via SMS
2. **Test payments:** Cash and Wallet work immediately. Paystack/Flutterwave need your API keys
3. **Admin Panel:** Live at http://148.230.120.40 (credentials provided)
4. **Maps:** If maps show gray, the Maps SDK needs enabling in Google Cloud Console
5. **Vehicle images:** Currently showing placeholder — will update when images are ready

### What Client Needs to Provide
| Item | Purpose | Priority |
|------|---------|----------|
| Flutterwave API keys (public, secret, encryption) | Split payments (2.5% royalty) | High |
| Paystack API keys (public, secret) | Nigerian payment gateway | High |
| Termii API key + Sender ID | SMS OTP for registration | High |
| Domain (9jaridepro.com) registration confirmation | SSL + professional URL | Medium |
| Vehicle type images (Economy, Premium, SUV) | Display in ride booking | Medium |

---

## 12. Remaining Work

### Phase 1 — Ready to Complete
| Task | Status | Blocker |
|------|--------|---------|
| Customer APK release build | Done | — |
| Driver APK release build | Done | — |
| Admin Panel live | Done | — |
| Landing Page live | Done | — |
| Owner Panel live | Done | — |
| Firestore populated | Done | — |
| Branding complete | Done | — |
| Kill Switch working | Done | — |
| HP module working | Done | — |

### Phase 1 — Waiting on Client
| Task | Blocker |
|------|---------|
| Flutterwave split payment | Client API keys |
| Paystack integration | Client API keys |
| Termii SMS OTP | Client API keys |
| Domain + SSL | Client domain registration |

### Phase 2 (Future)
- Guardian AI (advanced security)
- Vehicle hardware kill switch (requires telematics API)
- Flutterwave sub-accounts (if complex)
- Advanced analytics and reporting
- iOS App Store deployment

---

## Security Notes

### Files NEVER to Commit
- `.env` files (server config with DB passwords)
- `firebase.json` / `firebase-admin-key.json` (Firebase credentials)
- `google-services.json` (Firebase client config)
- `key.properties` (signing passwords)
- `*.jks` (signing keystores)
- `*.apk` (build artifacts)

### .gitignore Covers
All above patterns are in the root `.gitignore`. Verified 2026-04-07.

---

## File Structure Reference

```
d:\Freelancer-Project\Oladoyin\
├── .gitignore
├── guide.md                          ← This file
├── project_status&plan.md            ← Detailed status + changelog
├── status.md                         ← Quick status overview
├── prompt.md                         ← Claude session context
├── chat.md                           ← Client chat history
├── 9jaridepro-release.jks            ← Signing keystore (DO NOT COMMIT)
├── scripts/
│   ├── firebase-admin-key.json       ← Firebase service account (DO NOT COMMIT)
│   ├── populate_firestore.js         ← Initial Firestore population
│   ├── fix_firestore.js              ← Payment model + subscription fix
│   ├── fix_firestore2.js             ← subscription_model + Paystack sandbox
│   ├── generate_icons.js             ← App icon generator
│   ├── generate_splash_logo.js       ← Splash logo generator
│   └── create_indexes.js             ← Firestore index reference
└── GoRide_V5.6_Source_Code/
    ├── Admin Panel - Landing Panel - Owner Panel/
    │   ├── Admin Panel/              ← Laravel (LIVE at :80)
    │   ├── Landing Panel/            ← Laravel (LIVE at :8081)
    │   └── Owner Panel/              ← Laravel (LIVE at :8082)
    └── Applications/GoRide-5.6/
        ├── customer/                 ← Flutter (com.njaridepro.customer)
        ├── driver/                   ← Flutter (com.njaridepro.driver)
        └── owner/                    ← Flutter (com.njaridepro.owner)
```
