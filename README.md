Desktop POS System — Enterprise Level
===================================

Overview
--------
This repository contains:
- Laravel backend API (app/) for multi-branch POS with Sanctum authentication
- Migrations, models, controllers, DTOs, services and events for orders, shifts, payments, KDS
- Seeders for initial demo data

What I implemented so far
-------------------------
- Database migrations for branches, users, roles, products, categories, customers, addresses, orders, items, payments, shifts
- Eloquent models for core tables
- API controllers: Auth, Product, Order, Shift, Payment, Customer, CustomerAddress, DeliveryPerson, Report
- DTOs and `OrderService` + `OrderRepository`
- Request validators for main actions
- Basic events and listeners for `NewOrderEvent`, `OrderReadyEvent`, `ShiftClosedEvent`
- Broadcasting channels and minimal `config/broadcasting.php` placeholders
- Seeders for roles, branches, users, categories, products, delivery persons
- Provider bindings for repositories

Next major steps
----------------
1. Install packages:
   - `spatie/laravel-permission` (roles & permissions)
   - WebSocket server (Laravel Reverb or Pusher client) for realtime events
   - Optional: `mike42/escpos-php` for server-side printing
2. Finish Electron POS and KDS scaffolds (separate projects)
3. Implement thermal printing integration and print templates
4. Add tests and CI

Local setup (development)
-------------------------
1. Install PHP dependencies (run in project root):

```powershell
composer install
```

2. Copy and update environment:

```powershell
copy .env.example .env
# update DB_CONNECTION, DB_DATABASE, BROADCAST_DRIVER etc
php artisan key:generate
```

3. Run migrations and seeders:

```powershell
php artisan migrate
php artisan db:seed
```

4. Start local server and (optionally) WebSocket server:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
# If using laravel-reverb or laravel-websockets, follow their README to run socket server
```

5. Run queue worker (events/listeners may be queued):

```powershell
php artisan queue:work --tries=3
```

API quick checks
----------------
- `POST /api/login` — returns Sanctum token
- `GET /api/products` — requires `Authorization: Bearer <token>`
- `POST /api/orders` — create order (see `OrderStoreRequest` rules)

Composer packages to install (suggested)
--------------------------------------
```powershell
composer require spatie/laravel-permission
composer require pusher/pusher-php-server
# or, if using Laravel Reverb (self-hosted), install required packages per project docs
```

Broadcasting / WebSocket notes
-----------------------------
- `config/broadcasting.php` contains placeholders for `pusher` and `reverb` configurations.
- Channels are defined in `routes/channels.php` with branch-based auth checks.
- To test realtime, set `BROADCAST_DRIVER=pusher` and configure `PUSHER_*` env vars or run a local socket server.

Electron apps
-------------
I will scaffold two Electron projects:
- `electron-pos/` (POS desktop) with Vue 3 + Pinia + Axios
- `electron-kds/` (Kitchen Display) with minimal real-time subscriber to `branch.{id}.kitchen`

I'll provide scaffolding commands once you want me to create Electron projects here or separately.

If you want me to continue now, choose one of:
- Finish package install and update Laravel config (I can provide exact composer commands and config edits)
- Finish backend features (listeners, more validators, tests)
- Scaffold Electron POS + KDS projects

---
