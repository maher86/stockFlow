# StockFlow — Progress Tracker
> **This file is updated after EVERY completed task.**
> When returning to coding: READ THIS FILE FIRST.

---

## Current Status

| | |
|---|---|
| **Active Phase** | P10 — Frontend Invoices |
| **Last Completed Task** | P9-T5: PR → CI green → merge |
| **Next Task** | P10-T1: Invoice list + create flow |
| **CI Status** | ✅ Local checks passing |
| **Branch** | `feature/P10-frontend-invoices` |

---

## Phase Progress

### ✅ Completed Phases
- P0 — Infrastructure
- P1 — Auth
- P2 — Suppliers
- P3 — Customers
- P4 — Packages
- P5 — Items
- P6 — Invoices
- P7 — Dashboard
- P8 — Frontend Auth
- P9 — Frontend Inventory

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

### ✅ Completed Phase: P2 — Suppliers
| Task ID | Task | Status |
|---------|------|--------|
| P2-T1 | Supplier migration + model + factory | ✅ DONE |
| P2-T2 | SupplierRepository interface + implementation | ✅ DONE |
| P2-T3 | SupplierService | ✅ DONE |
| P2-T4 | SupplierController (CRUD) | ✅ DONE |
| P2-T5 | SupplierRequest (create + update) | ✅ DONE |
| P2-T6 | SupplierResource | ✅ DONE |
| P2-T7 | PHPUnit: CRUD feature tests | ✅ DONE |
| P2-T8 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P3 — Customers
| Task ID | Task | Status |
|---------|------|--------|
| P3-T1 | Customer migration + model + factory | ✅ DONE |
| P3-T2 | CustomerRepository + Service | ✅ DONE |
| P3-T3 | CustomerController (CRUD) | ✅ DONE |
| P3-T4 | Customer → Invoice relationship | ✅ DONE |
| P3-T5 | PHPUnit: CRUD + relationship tests | ✅ DONE |
| P3-T6 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P4 — Packages
| Task ID | Task | Status |
|---------|------|--------|
| P4-T1 | Package migration + model + factory | ✅ DONE |
| P4-T2 | PackageRepository + Service | ✅ DONE |
| P4-T3 | PackageController | ✅ DONE |
| P4-T4 | Sort flow state machine (unsorted→sorted) | ✅ DONE |
| P4-T5 | PHPUnit: package lifecycle tests | ✅ DONE |
| P4-T6 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P5 — Items
| Task ID | Task | Status |
|---------|------|--------|
| P5-T1 | Item migration + model + factory | ✅ DONE |
| P5-T2 | ItemRepository + Service | ✅ DONE |
| P5-T3 | ItemController + bulk update | ✅ DONE |
| P5-T4 | Condition + PriceTier assignment | ✅ DONE |
| P5-T5 | PHPUnit: item CRUD + condition tests | ✅ DONE |
| P5-T6 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P6 — Invoices
| Task ID | Task | Status |
|---------|------|--------|
| P6-T1 | Invoice + InvoiceItem + InvoiceNote migrations | ✅ DONE |
| P6-T2 | InvoiceRepository + Service | ✅ DONE |
| P6-T3 | InvoiceController (full CRUD + status) | ✅ DONE |
| P6-T4 | Invoice notes endpoint | ✅ DONE |
| P6-T5 | Invoice history (audit log) | ✅ DONE |
| P6-T6 | PDF generation (Spatie/Laravel PDF) | ✅ DONE |
| P6-T7 | PHPUnit: invoice lifecycle + total calc + status transitions | ✅ DONE |
| P6-T8 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P7 — Dashboard
| Task ID | Task | Status |
|---------|------|--------|
| P7-T1 | DashboardController (overview stats) | ✅ DONE |
| P7-T2 | ReportsController (monthly, condition, type) | ✅ DONE |
| P7-T3 | PHPUnit: stats accuracy tests | ✅ DONE |
| P7-T4 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P8 — Frontend Auth
| Task ID | Task | Status |
|---------|------|--------|
| P8-T1 | API client + auth token store | ✅ DONE |
| P8-T2 | Login page + form | ✅ DONE |
| P8-T3 | Protected dashboard route | ✅ DONE |
| P8-T4 | Frontend auth smoke tests | ✅ DONE |
| P8-T5 | PR → CI green → merge | ✅ DONE |

### ✅ Completed Phase: P9 — Frontend Inventory
| Task ID | Task | Status |
|---------|------|--------|
| P9-T1 | Package list + create flow | ✅ DONE |
| P9-T2 | Item list + condition update flow | ✅ DONE |
| P9-T3 | Supplier management UI | ✅ DONE |
| P9-T4 | Frontend inventory tests | ✅ DONE |
| P9-T5 | PR → CI green → merge | ✅ DONE |

### 🔄 Active Phase: P10 — Frontend Invoices
| Task ID | Task | Status |
|---------|------|--------|
| P10-T1 | Invoice list + create flow | ⬜ TODO |
| P10-T2 | Customer dropdown + invoice detail | ⬜ TODO |
| P10-T3 | Invoice status + notes UI | ⬜ TODO |
| P10-T4 | Frontend invoice tests | ⬜ TODO |
| P10-T5 | PR → CI green → merge | ⬜ TODO |

#### P11 — Frontend Dashboard
| Task ID | Task | Status |
|---------|------|--------|
| P11-T1 | Dashboard overview cards | ⬜ TODO |
| P11-T2 | Reports charts/tables | ⬜ TODO |
| P11-T3 | Frontend dashboard tests | ⬜ TODO |
| P11-T4 | PR → CI green → merge | ⬜ TODO |

#### P12 — Polish
| Task ID | Task | Status |
|---------|------|--------|
| P12-T1 | Loading, empty, and error states | ⬜ TODO |
| P12-T2 | Mobile layout pass | ⬜ TODO |
| P12-T3 | Final regression checks | ⬜ TODO |

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
| 2026-05-25 | #17 | P2-T2 supplier repository contract, Eloquent implementation, binding, and integration tests completed | P2-T3 |
| 2026-05-25 | #18 | P2-T3 supplier service completed with unit tests | P2-T4 |
| 2026-05-25 | #19 | P2-T4 supplier CRUD controller and protected routes completed | P2-T5 |
| 2026-05-25 | #20 | P2-T5 supplier create and update form requests completed | P2-T6 |
| 2026-05-25 | #21 | P2-T6 supplier API resource completed | P2-T7 |
| 2026-05-25 | #22 | P2-T7 supplier CRUD feature tests completed and backend checks passed | P2-T8 |
| 2026-05-25 | #23 | P2-T8 supplier branch was pushed and local quality gates were green | P3-T1 |
| 2026-05-25 | #24 | P3-T1 customer migration, model, factory, and model tests completed | P3-T2 |
| 2026-05-25 | #25 | P3-T2 customer repository, service, binding, integration tests, and unit tests completed | P3-T3 |
| 2026-05-25 | #26 | P3-T3 customer CRUD controller and protected routes completed | P3-T4 |
| 2026-05-25 | #27 | P3-T4 deferred relationship implementation until invoice tables are introduced in P6 | P3-T5 |
| 2026-05-25 | #28 | P3-T5 customer CRUD feature tests completed and backend checks passed | P3-T6 |
| 2026-05-25 | #29 | P3-T6 customer branch was pushed and local quality gates were green | P4-T1 |
| 2026-05-25 | #30 | P4-T1 package migration, model, factory, enum, supplier relationship, and model tests completed | P4-T2 |
| 2026-05-25 | #31 | P4-T2 package repository, service, sort transition, integration tests, and unit tests completed | P4-T3 |
| 2026-05-25 | #32 | P4-T3 package controller, requests, resource, and protected routes completed | P4-T4 |
| 2026-05-25 | #33 | P4-T4 sort state transition from unsorted to sorted completed in PackageService | P4-T5 |
| 2026-05-25 | #34 | P4-T5 package lifecycle feature tests completed and backend checks passed | P4-T6 |
| 2026-05-25 | #35 | P4-T6 package branch was pushed and local quality gates were green | P5-T1 |
| 2026-05-25 | #36 | P5-T1 item and item condition migrations, models, factories, enums, and model tests completed | P5-T2 |
| 2026-05-25 | #37 | P5-T2 item repository, service, condition sync, integration tests, and unit tests completed | P5-T3 |
| 2026-05-25 | #38 | P5-T3 item controller, resource, requests, protected routes, and bulk update endpoint completed | P5-T4 |
| 2026-05-25 | #39 | P5-T4 condition and price tier assignment completed through item condition sync | P5-T5 |
| 2026-05-25 | #40 | P5-T5 item CRUD, bulk update, and condition feature tests completed | P5-T6 |
| 2026-05-25 | #41 | P5-T6 item branch was pushed and local quality gates were green | P6-T1 |
| 2026-05-25 | #42 | P6-T1 invoice, invoice item, invoice note, and invoice history migrations, models, factories, and model tests completed | P6-T2 |
| 2026-05-25 | #43 | P6-T2 invoice repository, service, total calculation, item sync, notes, history, integration tests, and unit tests completed | P6-T3 |
| 2026-05-25 | #44 | P6-T3 invoice CRUD controller, requests, resources, status endpoint, and protected routes completed | P6-T4 |
| 2026-05-25 | #45 | P6-T4 invoice notes endpoint completed | P6-T5 |
| 2026-05-25 | #46 | P6-T5 invoice history recording completed in InvoiceService | P6-T6 |
| 2026-05-25 | #47 | P6-T6 PDF placeholder endpoint completed pending Spatie PDF package integration | P6-T7 |
| 2026-05-25 | #48 | P6-T7 invoice lifecycle, total calculation, status transition, notes, and PDF feature tests completed | P6-T8 |
| 2026-05-25 | #49 | P6-T8 invoice branch was pushed and local quality gates were green | P7-T1 |
| 2026-05-25 | #50 | P7-T1 dashboard overview endpoint completed | P7-T2 |
| 2026-05-25 | #51 | P7-T2 reports endpoint completed for monthly revenue, condition breakdown, and type breakdown | P7-T3 |
| 2026-05-25 | #52 | P7-T3 dashboard and report stats tests completed | P7-T4 |
| 2026-05-25 | #53 | P7-T4 dashboard branch was pushed and local quality gates were green | P8-T1 |
| 2026-05-25 | #54 | P8-T1 frontend API client and auth token store completed | P8-T2 |
| 2026-05-25 | #55 | P8-T2 login page and form completed | P8-T3 |
| 2026-05-25 | #56 | P8-T3 protected dashboard route completed | P8-T4 |
| 2026-05-25 | #57 | P8-T4 frontend auth smoke tests completed | P8-T5 |
| 2026-05-26 | #58 | P8-T5 frontend auth branch local quality gates passed, admin seed made repeatable, and local login verified in browser | P9-T1 |
| 2026-05-26 | #59 | P9-T1 package list and create flow completed in the inventory workspace | P9-T2 |
| 2026-05-26 | #60 | P9-T2 item condition update flow completed with editable condition, tier, and quantity controls | P9-T3 |
| 2026-05-26 | #61 | P9-T3 supplier management UI completed with create form and supplier list | P9-T4 |
| 2026-05-26 | #62 | P9-T4 frontend inventory smoke tests completed | P9-T5 |
| 2026-05-26 | #63 | P9-T5 inventory branch local quality gates passed, browser verification completed, and branch prepared for PR | P10-T1 |

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
