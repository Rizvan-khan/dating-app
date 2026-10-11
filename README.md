# Bareilly Hearts - Only for Bareilly

Full-stack dating app scaffold with a React/Vite PWA frontend and Laravel 11 REST API backend.

## Current status

The frontend runs as an interactive local preview with sample profiles, area filters, likes, sample chat with credit deductions, and device-local profile settings. It is not connected to the backend. Live phone OTP, payments, real-time messaging, and audio/video calls are not enabled.

The backend contains Laravel application modules, migrations, and Filament resources. It is not yet a complete bootstrapped Laravel installation and has not been run or tested with PHP/Composer. Production deployment requires completing the Laravel skeleton, authorization review, service integrations, and backend testing.

## Highlights

- Bareilly-only onboarding with GPS radius validation around `28.3670, 79.4304` and area selection.
- Phone OTP login, immutable gender selection, 18+ validation, 2-6 photos, bio, interests-ready profile model.
- Female safety: free access, verification badge, hide location, incognito, report and block.
- Opposite-gender discovery, match-on-mutual-like, chat/call unlock after match.
- Male credit rules: welcome 10 credits, chat 2 credits/message, audio 5 credits/min, video 10 credits/min.
- Razorpay order + signature verification flow and wallet history.
- Laravel Sanctum, Reverb/Pusher-ready broadcasting, Agora/WebRTC call token placeholder.
- Filament admin resources for users, packages, transactions, and reports.

## Local frontend

```bash
cd frontend
npm install
npm run dev
```

## Backend setup

PHP and Composer are required. First create a Laravel 11 application and merge the supplied backend modules into its generated skeleton. The following commands apply after the skeleton and service providers have been configured.

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan sanctum:install
php artisan reverb:start
php artisan serve
```

Seed packages with:

```bash
php artisan db:seed --class=CreditPackageSeeder
```

Configure `RAZORPAY_KEY`, `RAZORPAY_SECRET`, and Agora credentials before production use.
