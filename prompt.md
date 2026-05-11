# 9jaRide Pro — New Claude Session Prompt

Copy everything below this line and paste it as your first message in a new Claude Code session.

---

I'm continuing work on the **9jaRide Pro** project — a Nigerian taxi bidding app for client Eagles Partners (Oladoyin). Phase 1 budget is $2,200. The previous Claude session built the release APKs and they're ready for client testing.

## Read These First (in order)

1. **`d:\Freelancer-Project\Oladoyin\handoff.md`** — Complete handoff with all credentials, what's done, what's pending. **READ THIS FIRST.**
2. **`d:\Freelancer-Project\Oladoyin\guide.md`** — Full project documentation
3. **`d:\Freelancer-Project\Oladoyin\project_status&plan.md`** — Detailed status and full changelog
4. **`d:\Freelancer-Project\Oladoyin\status.md`** — Quick status overview
5. **`d:\Freelancer-Project\Oladoyin\chat.md`** — Original client chat history (only if needed)

## Memory Files (auto-loaded — already in context)
- `C:\Users\com\.claude\projects\d--Freelancer-Project-Oladoyin\memory\` contains persistent rules and project info

## Key Things to Know

### Project Locations
- **Project root:** `d:\Freelancer-Project\Oladoyin`
- **Git repo:** https://github.com/slotservice/9jaRide_Pro
- **VPS:** 148.230.120.40 (root, key auth)
- **Admin Panel:** http://148.230.120.40 (admin@9jaridepro.com / 9jaRide@2026!)
- **Landing:** http://148.230.120.40:8081
- **Owner Panel:** http://148.230.120.40:8082

### Stack
- **Backend:** Laravel 10 + MySQL + Nginx (live on VPS)
- **Mobile:** Flutter 3.41.6 + Firebase (jaride-pro)
- **Theme:** Deep Green (#1B5E20) + Gold (#D4AF37)
- **Packages:** com.njaridepro.customer / com.njaridepro.driver / com.njaridepro.owner

### APKs (ready at project root)
- `9jaRidePro-Customer.apk` (59MB, arm64) — for client
- `9jaRidePro-Driver.apk` (61MB, arm64) — for client
- `9jaRidePro-Customer-x86.apk` (62MB) — for LDPlayer testing
- `9jaRidePro-Driver-x86.apk` (63MB) — for LDPlayer testing

### What's Pending (Waiting on Client)
- Flutterwave API keys (split payments 2.5% royalty)
- Paystack API keys (current placeholders cause "Something went wrong" on topup)
- Termii API keys (SMS OTP)
- Domain 9jaridepro.com registration (for SSL)
- Vehicle type images (Economy/Premium/SUV — currently gray placeholder)

### Critical Rules
1. **Never modify client-provided assets** — logos/images stay as-is unless user asks
2. **Always use git push/pull** — no direct file uploads to server
3. **Never commit:** `.env`, `*.jks`, `google-services.json`, `firebase-admin-key.json`, `key.properties`, `*.apk`
4. **Don't auto-rebuild APKs** — only when user explicitly says to (builds take 2-5 min)
5. **Make all code fixes first**, build once when user requests

### Build Commands
```bash
cd "d:/Freelancer-Project/Oladoyin/GoRide_V5.6_Source_Code/Applications/GoRide-5.6/customer"
C:/flutter/bin/flutter build apk --release --split-per-abi

cd "d:/Freelancer-Project/Oladoyin/GoRide_V5.6_Source_Code/Applications/GoRide-5.6/driver"
C:/flutter/bin/flutter build apk --release --split-per-abi

# Output: build/app/outputs/flutter-apk/app-arm64-v8a-release.apk
```

### Server Deploy
```bash
git push origin main
ssh root@148.230.120.40
cd /var/www/9jaride-admin && git pull origin main && php artisan cache:clear
```

### Firestore Population Scripts (in scripts/)
- `populate_firestore.js` — initial setup (already run)
- `fix_firestore.js`, `fix_firestore2.js`, `fix_zones.js` — fixes (already run)
- Need `firebase-admin-key.json` in scripts/ to run any of them

## Current Task

The previous session completed Milestone 3 — the APK files are built and ready to send to the client. The client said:

> "I will use new claude session, but new claude session must be can handle this task as well like previous session"

**Please start by reading `handoff.md` and confirm you have all context, then ask what I want to work on next.**

Common things that might come up:
- Client reports a bug after testing → diagnose and fix
- Client provides Flutterwave/Paystack/Termii keys → integrate them
- Need to push code to GitHub
- Need to deploy server changes
- Need to rebuild APKs after fixes
- Vehicle images arrive → upload to Firebase Storage

## Milestone Status

| M | Scope | Amount | Status |
|---|---|---|---|
| M1 | Admin Dashboard | $400 | PAID + DELIVERED |
| M2 | HP Module + Kill Switch | part of $1,800 | DELIVERED |
| M3 | Flutter APKs + full platform | remainder | APKs ready for client testing |

**There is no formal Milestone 4** — Phase 1 ends when M3 accepted. Phase 2 mentioned but undefined.
