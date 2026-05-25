# StockFlow — Session Start Checklist
> Run this EVERY time you open the project, before writing a single line of code.

---

## Step 1 — Orient yourself (2 minutes)

```bash
# 1. Pull latest changes
git pull origin develop

# 2. Check current status
make status

# 3. Read the progress file
cat docs/PROGRESS.md | head -40
```

**Answer these questions before coding:**
- What is the active phase?
- What is the next task?
- Are there any open PRs to merge first?
- Are there any failing tests to fix?

---

## Step 2 — Start containers

```bash
make up
# Wait for healthy status
docker compose ps
```

---

## Step 3 — Pick your task

Open `docs/PROGRESS.md` and find the first ⬜ TODO in the active phase.

Example:
```
Active Phase: P2 — Suppliers
Next Task: P2-T3 — SupplierService
```

---

## Step 4 — Code → Test → Commit loop

```bash
# After writing code for a task:
make test-backend

# If green:
git add .
git commit -m "P2: Add SupplierService - all tests pass"

# Update PROGRESS.md: change ⬜ TODO → ✅ DONE
# Update Next Task field
git add docs/PROGRESS.md
git commit -m "P2: Update progress tracker"
```

---

## Step 5 — End of session

```bash
# Make sure everything is committed
git status

# Push to feature branch
git push origin feature/P2-suppliers

# Note what you were doing in PROGRESS.md Session Log
```

---

## Quick Reference

| Command | What it does |
|---------|-------------|
| `make up` | Start all Docker containers |
| `make test` | Run all backend + frontend tests |
| `make test-backend` | PHPUnit only |
| `make test-frontend` | Vitest only |
| `make migrate` | Run migrations |
| `make fresh` | Fresh DB + seed (careful!) |
| `make shell` | SSH into backend |
| `make routes` | List all API routes |
| `make status` | Show test status + current progress |
| `make logs-backend` | Watch backend logs |

---

## File Locations Quick Reference

```
docs/BLUEPRINT.md          → Architecture, tech stack, domain model
docs/PROGRESS.md           → Current phase, tasks, session log
docs/CODEX_AGENT.md        → Codex system prompt (paste when starting Codex)

backend/app/Domain/        → Business logic (no framework deps)
backend/app/Services/      → Application services
backend/app/Repositories/  → DB access layer
backend/app/Http/Controllers/Api/V1/  → Thin HTTP controllers
backend/tests/Feature/     → Endpoint tests
backend/tests/Unit/        → Service + domain tests

frontend/src/types/        → TypeScript types (mirror backend exactly)
frontend/src/services/     → API client
frontend/src/stores/       → Zustand state
frontend/src/components/   → UI components
```
