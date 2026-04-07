# 9jaRide Pro - Claude Session Prompt

Copy everything below this line and paste it as your first message in a new Claude Code session.

---

I'm working on the 9jaRide Pro project - a taxi bidding app for a Nigerian client (Eagles Partners). This is a freelancer project at $2,200 for Phase 1.

## Project Location & Access

- **Project directory:** d:\Freelancer-Project\Oladoyin
- **Git repo:** https://github.com/slotservice/9jaRide_Pro.git
- **VPS:** 148.230.120.40 (Ubuntu 22.04, SSH key auth as root)
- **Status file:** d:\Freelancer-Project\Oladoyin\status.md (READ THIS FIRST - has full project status)
- **Chat history:** d:\Freelancer-Project\Oladoyin\chat.md (client requirements/conversations)
- **Memory files:** C:\Users\com\.claude\projects\d--Freelancer-Project-Oladoyin\memory\ (persistent context)

## What's LIVE (don't break these)

- Admin Panel: http://148.230.120.40 (admin@9jaridepro.com / 9jaRide@2026!)
- Landing Page: http://148.230.120.40:8081
- Owner Panel: http://148.230.120.40:8082
- All three are Laravel 10 + MySQL + Nginx

## Tech Stack

- **Admin/Landing/Owner:** Laravel 10 + MySQL + Nginx on VPS
- **Mobile apps:** Flutter + Firebase (Customer + Driver + Owner)
- **Firebase project:** jaride-pro (Spark plan, Firestore/Auth/Realtime DB)
- **Theme:** Deep Green (#1B5E20) + Gold (#D4AF37)
- **Package IDs:** com.njaridepro.customer, com.njaridepro.driver

## Server Details

- MySQL: njaride_user / N9jaR1de_Pr0_2026! (DBs: njaride_admin, njaride_owner)
- Flutter SDK: C:\flutter (local) + /opt/flutter (VPS)
- Android SDK: C:\android-sdk (local)
- Java 17 installed locally

## Source Code Structure

```
d:\Freelancer-Project\Oladoyin\
├── GoRide_V5.6_Source_Code/
│   ├── Admin Panel - Landing Panel - Owner Panel/
│   │   ├── Admin Panel/          (Laravel - LIVE at :80)
│   │   ├── Landing Panel/        (Laravel - LIVE at :8081)
│   │   └── Owner Panel/          (Laravel - LIVE at :8082)
│   └── Applications/GoRide-5.6/
│       ├── customer/             (Flutter - com.njaridepro.customer)
│       ├── driver/               (Flutter - com.njaridepro.driver)
│       └── owner/                (Flutter - com.goride.owner, NOT yet rebranded)
├── 9jaRidePro-Customer.apk       (debug build, 212MB)
├── 9jaRidePro-Driver.apk         (debug build, 245MB)
├── status.md                     (FULL project status - read this!)
├── chat.md                       (client chat history)
└── prompt.md                     (this file)
```

## What's Been Completed

1. Admin Dashboard with RBAC, Firebase integration (Milestone 1 - $400)
2. HP Module + Kill Switch API (Milestone 2)
3. Landing Page with client images and animations
4. Owner Panel with Phone OTP login
5. Flutter apps fully rebranded (com.goride -> com.njaridepro for customer + driver)
6. Firebase config fixed (correct Android appIds in firebase_options.dart)
7. Startup bug fixes: error handling in main.dart, splash_controller, global_setting_controller, fire_store_utils (null safety), onboarding skip when Firestore empty
8. Debug APKs built and ready for LDPlayer testing

## What's Remaining for Phase 1

### Immediate Priority
- **Test APKs on LDPlayer** - verify splash -> login flow works end-to-end
- **Fix release APK builds** - currently failing with "Dart snapshot generator failed with exit code 1" (AOT compilation error on all architectures: arm, arm64, x64). Likely Dart SDK version mismatch with dependencies. Try: check pubspec.yaml SDK constraints, update dependencies, or downgrade Flutter
- **Populate Firestore settings** - the apps load settings from Firestore collections (settings/globalValue, settings/globalKey, etc.). These need to be populated with 9jaRide Pro values for the app to fully function

### Waiting on Client
- **Domain:** Client registering 9jaridepro.com -> needs SSL/Nginx setup when ready
- **Flutterwave/Paystack:** Sandbox payment integration (waiting on client API keys)
- **Termii SMS OTP:** Integration (waiting on client API keys)
- **App icons:** Still GoRide icons, need 9jaRide Pro branded icons from client

### Technical Tasks
- Set up proper signing keystores for release APK builds
- Enable Maps SDK in Google Cloud Console for jaride-pro project
- Move FCM server key from config/constant.php to .env (security fix)
- Owner Flutter app still has com.goride package names (7 files need updating when needed)
- Investigate and fix release APK AOT snapshot failure

## Build Commands

```bash
# Flutter builds (from customer/ or driver/ directory)
C:/flutter/bin/flutter clean
C:/flutter/bin/flutter build apk --debug    # For testing (works)
C:/flutter/bin/flutter build apk --release  # Currently failing - needs fix

# gradle.properties has kotlin.incremental=false (required for Windows cross-drive builds)
```

## Important Rules

1. **Never modify client-provided images/logos without asking**
2. **Always use git push/pull workflow** - code changes locally, push to GitHub, pull on server
3. **Server config (.env, nginx, firebase.json) is done directly on server** - don't commit these
4. **APK files are gitignored** - don't try to commit them
5. **Read status.md first** before doing any work - it has the latest project state
6. **Read chat.md** for client requirements context when needed

## Git Workflow

```bash
# Local development
cd d:/Freelancer-Project/Oladoyin
# make changes...
git add <specific-files>
git commit -m "descriptive message"
git push origin main

# Server deployment
ssh root@148.230.120.40
cd /var/www/9jaride-admin  # or 9jaride-landing or 9jaride-owner
git pull origin main
```

Please start by reading status.md to understand the current state, then continue with the immediate priority tasks listed above.
