# 9jaRide Pro - Project Status

**Last Updated:** 2026-04-07
**Phase:** 1 ($2,200)
**Client:** Eagles Partners (Oladoyin)

---

## Completed

### Milestone 1 ($400) - Admin Dashboard
- Admin Panel LIVE at http://148.230.120.40
- Login: admin@9jaridepro.com / 9jaRide@2026!
- Full RBAC with Spatie permissions
- Firebase integration (Firestore, Auth, Realtime DB)
- Branding: Deep Green (#1B5E20) + Gold (#D4AF37)

### Milestone 2 - HP Module + Kill Switch
- Hire Purchase module (assign HP to drivers, daily deduction, status tracking)
- Kill Switch API (lock/unlock driver app remotely)
- Green/Yellow/Red status with auto-lockout
- LIVE at http://148.230.120.40

### Landing Page
- LIVE at http://148.230.120.40:8081
- Client images, scroll animations, CSS/JS in app.blade.php
- Firebase-powered template (Firestore landingPageTemplate)

### Owner Panel
- LIVE at http://148.230.120.40:8082
- Phone OTP login (Google sign-in hidden)
- Driver management, ride tracking, payouts, subscriptions

### Flutter Apps - Rebranding Complete
- Customer app: com.njaridepro.customer
- Driver app: com.njaridepro.driver
- Firebase project: jaride-pro (Spark plan)
- All com.goride references replaced in:
  - build.gradle.kts (namespace + applicationId)
  - MainActivity.kt (package + directory renamed)
  - AndroidManifest.xml (activity, notification channels, background service)
  - iOS project.pbxproj (bundle identifier + display name)
  - firebase_options.dart (correct Android appId + apiKey from google-services.json)
  - google-services.json (correct package names)

### Bug Fixes Applied (2026-04-07)
- Firebase options: Fixed web appId -> correct Android appId (was causing black screen)
- Customer AndroidManifest: Added missing INTERNET permission
- Google Maps API key: Replaced YOUR_API_KEY_HERE placeholder with actual key
- Onboarding: Skip to login when Firestore onboarding data is empty
- main.dart: Wrapped Firebase.initializeApp in try-catch
- splash_controller: Wrapped redirectScreen in try-catch, fallback to LoginScreen
- on_boarding_controller: Added .catchError() to prevent infinite loading
- global_setting_controller: Each Firestore call wrapped in try-catch with defaults
- fire_store_utils: getCurrentUid() null-safe, getSettings/getGoogleAPIKey null-safe
- AndroidManifest: Fixed background service from com.example.app to correct package
- gradle.properties: Added kotlin.incremental=false (fixes cross-drive build on Windows)

---

## In Progress

### Flutter APK Builds
- Debug APKs built and tested on LDPlayer (212MB customer, 245MB driver)
- Release APKs: Dart AOT snapshot failure - needs investigation
  - Error: "Dart snapshot generator failed with exit code 1" on all architectures
  - Likely Dart SDK version compatibility with some dependencies
- App icons: Still GoRide icons, need 9jaRide Pro icons from client

---

## Remaining (Phase 1)

### Waiting on Client
- **Domain:** Client registering 9jaridepro.com -> needs SSL setup
- **Flutterwave/Paystack:** Sandbox integration waiting on client API keys
- **Termii SMS OTP:** Waiting on client API keys
- **App icons:** Need 9jaRide Pro branded icons from client

### Technical Tasks
- Fix release APK build (investigate AOT snapshot failure)
- Set up signing keystores for release builds
- Google Maps API key: Enable Maps SDK in Google Cloud Console for jaride-pro project
- Populate Firestore settings collections (globalValue, globalKey, etc.)
- Move FCM server key from config/constant.php to .env (security)

---

## Server Details

| Item | Value |
|------|-------|
| VPS IP | 148.230.120.40 |
| OS | Ubuntu 22.04 |
| SSH | root, key auth |
| MySQL User | njaride_user / N9jaR1de_Pr0_2026! |
| Admin DB | njaride_admin |
| Owner DB | njaride_owner |
| Flutter (local) | C:\flutter |
| Flutter (VPS) | /opt/flutter |
| Android SDK | C:\android-sdk |
| Java | 17 |
| Git Repo | github.com/slotservice/9jaRide_Pro |

## Git Workflow
- Code changes locally -> git push -> git pull on server
- Server config (.env, nginx, firebase.json) done directly on server
