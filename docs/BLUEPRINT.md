# StockFlow — Master Blueprint
> **VERSION:** 1.0.0 | **LAST UPDATED:** 2026-05-25
> This file is the single source of truth. Every agent, every PR, every task references this file first.

---

## 1. Project Overview

**StockFlow** is a clothing inventory management SaaS built for UAE/Gulf markets.
Core flow: Receive package → Sort (unsorted/sorted) → Season → Gender → Type → Condition → Price → Invoice → Customer/Supplier.

### Tech Stack
| Layer | Technology |
|-------|-----------|
| Backend API | Laravel 11, PHP 8.3 |
| Database | MySQL 8.0 |
| Frontend | Next.js 14 (App Router), TypeScript, Tailwind CSS |
| State Management | Zustand + React Query (TanStack) |
| Auth | Laravel Sanctum (SPA tokens) |
| Testing | PHPUnit (backend), Vitest + Testing Library (frontend) |
| Containerization | Docker + Docker Compose |
| CI/CD | GitHub Actions |
| Queue | Laravel Horizon + Redis |
| Storage | Laravel Storage (S3-compatible, MinIO locally) |

---

## 2. Repository Structure

```
stockflow/
├── .github/
│   └── workflows/
│       ├── backend-ci.yml       # PHP lint + PHPUnit on every PR
│       ├── frontend-ci.yml      # Type-check + Vitest on every PR
│       └── deploy.yml           # Deploy on merge to main
├── backend/                     # Laravel 11 application
│   ├── app/
│   │   ├── Domain/              # Business logic (pure PHP, no framework deps)
│   │   │   ├── Inventory/
│   │   │   ├── Invoice/
│   │   │   ├── Customer/
│   │   │   └── Supplier/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/
│   │   │   ├── Requests/
│   │   │   ├── Resources/
│   │   │   └── Middleware/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   └── Jobs/
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   ├── tests/
│   │   ├── Unit/
│   │   ├── Feature/
│   │   └── Integration/
│   ├── routes/api.php
│   └── docker/
├── frontend/                    # Next.js 14 application
│   ├── src/
│   │   ├── app/                 # App Router pages
│   │   ├── components/
│   │   │   ├── ui/              # Base design system
│   │   │   ├── inventory/
│   │   │   ├── invoice/
│   │   │   ├── supplier/
│   │   │   └── customer/
│   │   ├── hooks/
│   │   ├── stores/              # Zustand stores
│   │   ├── services/            # API client
│   │   ├── types/               # TypeScript interfaces
│   │   └── lib/
│   └── tests/
├── docker/
│   ├── docker-compose.yml
│   ├── docker-compose.test.yml
│   └── nginx/
├── docs/
│   ├── BLUEPRINT.md             # ← YOU ARE HERE
│   ├── PROGRESS.md              # ← Updated after every phase
│   ├── API.md                   # Auto-generated API reference
│   └── DECISIONS.md             # Architecture decisions log
└── Makefile                     # Shortcuts: make test, make migrate, etc.
```

---

## 3. Domain Model

### Enums (shared across backend and frontend)

```
Season:    summer | winter | spring | all_seasons
Gender:    men | women | boys | girls
ItemType:  pants | shorts | shirt | skirt | jacket | dress | other
Condition: perfect | good | normal | zero
PriceTier: N3 | N2 | N1 | zero
Status (package): unsorted | sorted | processing | complete
Status (invoice): draft | pending | partial | paid | cancelled
```

### Database Tables
```
users
suppliers
customers
packages          (shipments received)
items             (individual clothing pieces)
item_conditions   (condition + price assignment)
invoices
invoice_items
invoice_notes
invoice_history   (audit log)
```

---

## 4. API Structure (V1)

Base URL: `/api/v1`
Auth: Bearer token (Sanctum)

```
# Auth
POST   /auth/login
POST   /auth/logout
GET    /auth/me

# Dashboard
GET    /dashboard/overview
GET    /dashboard/reports

# Packages (shipments)
GET    /packages
POST   /packages
GET    /packages/{id}
PATCH  /packages/{id}
POST   /packages/{id}/sort

# Items
GET    /items
POST   /items
GET    /items/{id}
PATCH  /items/{id}
POST   /items/bulk-update

# Suppliers
GET    /suppliers
POST   /suppliers
GET    /suppliers/{id}
PUT    /suppliers/{id}
DELETE /suppliers/{id}

# Customers
GET    /customers
POST   /customers
GET    /customers/{id}
PUT    /customers/{id}
DELETE /customers/{id}
GET    /customers/{id}/invoices

# Invoices
GET    /invoices
POST   /invoices
GET    /invoices/{id}
PUT    /invoices/{id}
PATCH  /invoices/{id}/status
POST   /invoices/{id}/notes
GET    /invoices/{id}/pdf
DELETE /invoices/{id}
```

---

## 5. Phase Plan

Each phase = one GitHub PR = all tests pass before merge.

| Phase | Name | Key Deliverables |
|-------|------|-----------------|
| **P0** | Infrastructure | Docker, CI/CD, empty Laravel + Next.js boilerplate |
| **P1** | Auth | Login, Sanctum, middleware, tests |
| **P2** | Suppliers | CRUD, validation, tests |
| **P3** | Customers | CRUD, linked to invoices, tests |
| **P4** | Packages | Receive, sort flow, status transitions, tests |
| **P5** | Items | Full item lifecycle, condition, pricing, tests |
| **P6** | Invoices | Create, update, status, notes, history, PDF, tests |
| **P7** | Dashboard | Overview stats, reports, tests |
| **P8** | Frontend Auth | Next.js login, protected routes, token management |
| **P9** | Frontend Inventory | Package + item management UI |
| **P10** | Frontend Invoices | Invoice manager (linked customers dropdown) |
| **P11** | Frontend Dashboard | Charts, reports, overview |
| **P12** | Polish | Error handling, loading states, empty states, mobile |

---

## 6. Testing Contract

Every PR **must** pass before merge:
- `composer test` → PHPUnit, minimum 80% coverage
- `npm run type-check` → Zero TypeScript errors
- `npm run test` → Vitest, all tests pass
- GitHub Actions CI green

Test naming convention:
```php
// Feature test
test('supplier can be created with valid data', function () { ... });
test('supplier creation fails without required name', function () { ... });

// Unit test
test('invoice calculates total correctly', function () { ... });
```

---

## 7. Git Workflow

```
main          ← production, protected, CI required
develop       ← integration branch
feature/P0-infrastructure
feature/P1-auth
feature/P2-suppliers
...
```

PR template: reference phase number, list tests added, confirm CI green.

---

## 8. Environment Variables

```bash
# Backend (.env)
APP_NAME=StockFlow
APP_ENV=local
DB_HOST=mysql
DB_DATABASE=stockflow
DB_USERNAME=stockflow
DB_PASSWORD=secret
REDIS_HOST=redis
QUEUE_CONNECTION=redis
SANCTUM_STATEFUL_DOMAINS=localhost:3000

# Frontend (.env.local)
NEXT_PUBLIC_API_URL=http://localhost:8000/api/v1
NEXT_PUBLIC_APP_NAME=StockFlow
```

---

## 9. Codex Agent Instructions

When Codex picks up a task it MUST:
1. Read `PROGRESS.md` first — find current phase and last completed task
2. Read this `BLUEPRINT.md` — understand the domain and structure
3. Never skip a test — every new class/service/endpoint gets a test
4. After completing a task, update `PROGRESS.md` with what was done
5. Follow the file structure exactly as defined in Section 2
6. Use Repository pattern for all DB access
7. Use Form Requests for all validation
8. Use API Resources for all responses
9. Never put business logic in controllers

---

## 10. Reference Checklist (when returning to coding)

Run this checklist every session start:
- [ ] Read `PROGRESS.md` → know current phase
- [ ] Run `make status` → see which tests pass/fail
- [ ] Check open PR on GitHub → continue or merge
- [ ] Pick next task from current phase in `PROGRESS.md`
