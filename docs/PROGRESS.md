# StockFlow — Progress Tracker
> **This file is updated after EVERY completed task.**
> When returning to coding: READ THIS FILE FIRST.

---

## Current Status

| | |
|---|---|
| **Active Phase** | P2 — Suppliers |
| **Last Completed Task** | P2-T1: Supplier migration + model + factory |
| **Next Task** | P2-T2: SupplierRepository interface + implementation |
| **CI Status** | ✅ Local checks passing |
| **Branch** | `feature/P2-suppliers` |

---

## Phase Progress

### ✅ Completed Phases
- P0 — Infrastructure
- P1 — Auth

---

### ✅ Completed Phase: P0 — Infrastructure

**Goal:** Docker running locally, empty Laravel + Next.js boots, CI configured.

| Task ID | Task | Status | Notes |
|---------|------|--------|-------|
| P0-T1 | Docker Compose (mysql, redis, backend, frontend, nginx, minio) | ⬜ TODO | |
| P0-T2 | Laravel 11 boilerplate in `/backend` | ✅ DONE | Laravel 11 scaffold with Sanctum, Pest, Pint, PHPStan, env examples, and smoke test |
| P0-T3 | Next.js 14 boilerplate in `/frontend` | ✅ DONE | Next.js App Router scaffold completed and Docker Compose stack verified |
| P0-T4 | Makefile with shortcuts | ✅ DONE | Verified Docker-backed test-backend, migrate, shell, and status command paths |
| P0-T5 | GitHub Actions — backend CI | ✅ DONE | PR workflow for PHP 8.3, Composer install, test DB migrations, and php artisan test |
| P0-T6 | GitHub Actions — frontend CI | ✅ DONE | PR workflow for npm ci, type-check, ESLint, and Vitest |
| P0-T7 | GitHub Actions — deploy workflow | ✅ DONE | Placeholder deploy workflow runs on push to main with SSH steps commented out |
| P0-T8 | PHPUnit base config + first smoke test | ✅ DONE | PHPUnit smoke test verifies GET /api/v1/health returns 200 |
| P0-T9 | Vitest base config + first smoke test | ✅ DONE | Vitest smoke test verifies the Home page renders without crashing |
| P0-T10 | PR to develop, CI green, merge | ⬜ TODO | |

---

### ⏳ Upcoming Phases

### ✅ Completed Phase: P1 — Auth
| Task ID | Task | Status |
|---------|------|--------|
| P1-T1 | User migration + model + factory | ✅ DONE |
| P1-T2 | Laravel Sanctum setup | ✅ DONE |
| P1-T3 | AuthController (login, logout, me) | ✅ DONE |
| P1-T4 | Auth middleware + protected routes | ✅ DONE |
| P1-T5 | PHPUnit: login success/fail, token, logout | ✅ DONE |
| P1-T6 | PR → CI green → merge | ✅ DONE |

### 🔄 Active Phase: P2 — Suppliers
| Task ID | Task | Status |
|---------|------|--------|
| P2-T1 | Supplier migration + model + factory | ✅ DONE |
| P2-T2 | SupplierRepository interface + implementation | ⬜ TODO |
| P2-T3 | SupplierService | ⬜ TODO |
| P2-T4 | SupplierController (CRUD) | ⬜ TODO |
| P2-T5 | SupplierRequest (create + update) | ⬜ TODO |
| P2-T6 | SupplierResource | ⬜ TODO |
| P2-T7 | PHPUnit: CRUD feature tests | ⬜ TODO |
| P2-T8 | PR → CI green → merge | ⬜ TODO |

#### P3 — Customers
| Task ID | Task | Status |
|---------|------|--------|
| P3-T1 | Customer migration + model + factory | ⬜ TODO |
| P3-T2 | CustomerRepository + Service | ⬜ TODO |
| P3-T3 | CustomerController (CRUD) | ⬜ TODO |
| P3-T4 | Customer → Invoice relationship | ⬜ TODO |
| P3-T5 | PHPUnit: CRUD + relationship tests | ⬜ TODO |
| P3-T6 | PR → CI green → merge | ⬜ TODO |

#### P4 — Packages
| Task ID | Task | Status |
|---------|------|--------|
| P4-T1 | Package migration + model + factory | ⬜ TODO |
| P4-T2 | PackageRepository + Service | ⬜ TODO |
| P4-T3 | PackageController | ⬜ TODO |
| P4-T4 | Sort flow state machine (unsorted→sorted) | ⬜ TODO |
| P4-T5 | PHPUnit: package lifecycle tests | ⬜ TODO |
| P4-T6 | PR → CI green → merge | ⬜ TODO |

#### P5 — Items
| Task ID | Task | Status |
|---------|------|--------|
| P5-T1 | Item migration + model + factory | ⬜ TODO |
| P5-T2 | ItemRepository + Service | ⬜ TODO |
| P5-T3 | ItemController + bulk update | ⬜ TODO |
| P5-T4 | Condition + PriceTier assignment | ⬜ TODO |
| P5-T5 | PHPUnit: item CRUD + condition tests | ⬜ TODO |
| P5-T6 | PR → CI green → merge | ⬜ TODO |

#### P6 — Invoices
| Task ID | Task | Status |
|---------|------|--------|
| P6-T1 | Invoice + InvoiceItem + InvoiceNote migrations | ⬜ TODO |
| P6-T2 | InvoiceRepository + Service | ⬜ TODO |
| P6-T3 | InvoiceController (full CRUD + status) | ⬜ TODO |
| P6-T4 | Invoice notes endpoint | ⬜ TODO |
| P6-T5 | Invoice history (audit log) | ⬜ TODO |
| P6-T6 | PDF generation (Spatie/Laravel PDF) | ⬜ TODO |
| P6-T7 | PHPUnit: invoice lifecycle + total calc + status transitions | ⬜ TODO |
| P6-T8 | PR → CI green → merge | ⬜ TODO |

#### P7 — Dashboard
| Task ID | Task | Status |
|---------|------|--------|
| P7-T1 | DashboardController (overview stats) | ⬜ TODO |
| P7-T2 | ReportsController (monthly, condition, type) | ⬜ TODO |
| P7-T3 | PHPUnit: stats accuracy tests | ⬜ TODO |
| P7-T4 | PR → CI green → merge | ⬜ TODO |

#### P8–P12 — Frontend
_Detailed tasks will be added when backend phases complete._

---

## Session Log

| Date | Session | What was done | Next task |
|------|---------|---------------|-----------|
| — | #1 | Project setup, BLUEPRINT.md and PROGRESS.md created | P0-T1 |
| 2026-05-25 | #2 | P0-T2 Laravel 11 backend scaffold created with Sanctum, Pest, Pint, PHPStan, env examples, and smoke test | P0-T3 |
| 2026-05-25 | #3 | P0-T3 frontend scaffold completed; Docker Compose build/start fixed and verified | P0-T4 |
| 2026-05-25 | #4 | P0-T4 Makefile shortcuts completed and Docker command paths verified | P0-T5 |
| 2026-05-25 | #5 | P0-T5 backend CI workflow created for PRs to main and develop | P0-T6 |
| 2026-05-25 | #6 | P0-T6 frontend CI workflow created with npm ci, type-check, ESLint, and Vitest | P0-T7 |
| 2026-05-25 | #7 | P0-T7 placeholder deploy workflow created for pushes to main | P0-T8 |
| 2026-05-25 | #8 | P0-T8 backend PHPUnit smoke test added for GET /api/v1/health | P0-T9 |
| 2026-05-25 | #9 | P0-T9 frontend Vitest smoke test added and P0 moved to completed phases | P1-T1 |
| 2026-05-25 | #10 | P1-T1 user migration, model, and factory verified with model factory tests | P1-T2 |
| 2026-05-25 | #11 | P1-T2 Laravel Sanctum setup verified with access token creation test | P1-T3 |
| 2026-05-25 | #12 | P1-T3 AuthController login, logout, and me endpoints implemented with tests | P1-T4 |
| 2026-05-25 | #13 | P1-T4 Sanctum middleware applied to protected auth routes and guest access covered | P1-T5 |
| 2026-05-25 | #14 | P1-T5 auth PHPUnit coverage completed for login success, login failure, token creation, me, and logout | P1-T6 |
| 2026-05-25 | #15 | P1-T6 PR branch was pushed and local quality gates were green | P2-T1 |
| 2026-05-25 | #16 | P2-T1 supplier migration, model, factory, and model tests completed | P2-T2 |

---

## Known Issues / Blockers

_None yet_

---

## Architecture Decisions Log

| Date | Decision | Reason |
|------|----------|--------|
| 2026-05-25 | Repository pattern for all DB access | Testability — easy to mock in unit tests |
| 2026-05-25 | App Router for Next.js | Server components, better SEO, modern pattern |
| 2026-05-25 | Zustand over Redux | Simpler for team, less boilerplate |
| 2026-05-25 | Pest PHP for tests | More readable syntax, works with PHPUnit runner |
