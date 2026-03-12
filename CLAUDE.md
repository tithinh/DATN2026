# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**FiveTech Store** — Cửa hàng bán phụ kiện điện thoại. Full-stack app with:
- `backend/` — Laravel 12 REST API
- `fivetech-store/` — Vue 3 + TypeScript SPA
- `fivetech_db.sql` — MySQL database dump

---

## Commands

### Backend (Laravel)

```bash
cd backend

# Install dependencies & setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Run dev server (port 8000)
php artisan serve

# Run tests
php artisan test
# or: composer test

# Run all dev services (server + queue + logs)
composer dev
```

### Frontend (Vue 3)

```bash
cd fivetech-store

# Install dependencies
npm install

# Dev server (port 5173, proxies /api → localhost:8000)
npm run dev

# Type-check + build
npm run build

# Preview production build
npm run preview
```

---

## Architecture

### API Design

All API routes are prefixed `/api/v1` (defined in `backend/routes/api.php`). Three route groups:

1. **Public** — Products, Categories, News, Auth (register/login), Contacts, Promotions
2. **Protected** (`auth:sanctum`) — User profile, Wishlist, Orders, Comments, Cart
3. **Admin** (`auth:sanctum` + admin check) — Dashboard stats, User management, full CRUD for all resources

Authentication uses **Laravel Sanctum token-based** (not cookie/session). Tokens stored in `localStorage` on frontend.

### Frontend API Client

`fivetech-store/src/api/index.js` — Single Axios instance with:
- `baseURL: http://localhost:8000/api/v1`
- Request interceptor: auto-attaches `Authorization: Bearer <token>` — checks `admin_token` first, then `user_token` in localStorage
- Response interceptor: on 401/419, clears tokens and redirects to `/login`

Vite dev proxy rewrites `/api/*` → `http://localhost:8000/api/v1/*`, so frontend can call `/api/products` without CORS issues in dev.

### State Management (Pinia)

| Store | Purpose |
|---|---|
| `auth.ts` | Customer login/register, user profile |
| `adminAuth.ts` | Admin login, role-based permissions (`super_admin` > `admin` > `manager` > `staff`) |
| `cart.ts` | Cart items, coupon, shipping fee calculation. Supports guest cart (hardcoded `user_id=1`) |
| `products.ts` | Product listing/filtering state |

### Router Guards (`fivetech-store/src/router/index.ts`)

`beforeEach` guard checks `adminAuth` store for admin routes and redirects to `/admin/login` if not authenticated. Customer routes have no guard; auth checks happen at component level.

### Layouts

- `MainLayout.vue` — Client-facing pages (header, footer, inner navigation)
- `AdminLayout.vue` — Admin panel (sidebar + header)
- Routes are nested under these layouts in `router/index.ts`

### Key Model Conventions (Backend)

- Models use non-standard primary keys: `product_id`, `order_id`, `order_item_id`, `cart_item_id`, etc. (not the default `id`)
- `Order` stores both `total_amount` (pre-discount) and `final_amount` (after discount)
- `OrderItem` has both `price_at_purchase` (DB column) and a `price` accessor (alias)
- Guest cart uses `user_id = 1` as a placeholder — not tied to a real user
- `ProductVariant` has a `price_extra` field added to base product price to get variant price

### Order Status Flow

`pending` → `confirmed` → `processing` → `shipping` → `delivered` → `completed`
Any status before `delivered` can transition to `cancelled`.

### Database

- MySQL, DB name: `fivetech_db`
- Import schema: `mysql -u root fivetech_db < fivetech_db.sql`
- DB config in `backend/.env`: `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_USERNAME=root`, `DB_PASSWORD=` (empty by default)

### Views Directory Structure

Frontend views follow a split:
- `src/views/cilent/` — Customer-facing pages (note: typo "cilent" not "client", keep consistent)
- `src/views/admin/` — Admin panel pages

### TypeScript Strictness

`tsconfig.app.json` has `strict: true`, `noUnusedLocals`, `noUnusedParameters` all enabled. The `api/index.js` is plain JS (not TS) to avoid typing issues with Axios interceptors.

---

## Key Integrations

- **Laravel Socialite**: Google + Facebook social login (`/auth/social/{provider}`)
- **Promotions/Coupons**: Validated via `POST /promotions/check` with coupon code; returns discount amount
- **Image uploads**: Product images stored via Laravel filesystem (`FILESYSTEM_DISK=local`); served through `/storage/` public path
- **SANCTUM_STATEFUL_DOMAINS**: Set to `localhost:5173,127.0.0.1:5173` — must match frontend dev port
