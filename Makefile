# StockFlow Makefile
# Usage: make <command>

.PHONY: help up down build test test-backend test-frontend migrate seed fresh logs shell status

# ─── Colors ──────────────────────────────────────────────────────────────────
GREEN  = \033[0;32m
YELLOW = \033[0;33m
RESET  = \033[0m

help: ## Show this help
	@echo ""
	@echo "$(GREEN)StockFlow — Available Commands$(RESET)"
	@echo "─────────────────────────────────────────"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-20s$(RESET) %s\n", $$1, $$2}'
	@echo ""

# ─── Docker ──────────────────────────────────────────────────────────────────
up: ## Start all containers
	docker compose up -d
	@echo "$(GREEN)✓ StockFlow running at http://localhost$(RESET)"

down: ## Stop all containers
	docker compose down

build: ## Rebuild all containers
	docker compose build --no-cache

restart: ## Restart all containers
	docker compose restart

# ─── Backend ─────────────────────────────────────────────────────────────────
migrate: ## Run database migrations
	docker compose exec backend php artisan migrate

migrate-test: ## Run migrations on test database
	docker compose exec backend php artisan migrate --env=testing

seed: ## Run database seeders
	docker compose exec backend php artisan db:seed

fresh: ## Fresh migration + seed (DESTROYS ALL DATA)
	docker compose exec backend php artisan migrate:fresh --seed

shell: ## SSH into backend container
	docker compose exec backend bash

shell-frontend: ## SSH into frontend container
	docker compose exec frontend sh

# ─── Testing ─────────────────────────────────────────────────────────────────
test: test-backend test-frontend ## Run ALL tests

test-backend: ## Run PHPUnit tests
	@echo "$(GREEN)Running backend tests...$(RESET)"
	docker compose exec backend php artisan test --parallel

test-backend-coverage: ## Run PHPUnit with coverage report
	docker compose exec backend php artisan test --coverage --min=80

test-backend-filter: ## Run specific test: make test-backend-filter FILTER=SupplierTest
	docker compose exec backend php artisan test --filter=$(FILTER)

test-frontend: ## Run Vitest tests
	@echo "$(GREEN)Running frontend tests...$(RESET)"
	docker compose exec frontend npm run test

test-frontend-coverage: ## Run Vitest with coverage
	docker compose exec frontend npm run test:coverage

type-check: ## TypeScript type check
	docker compose exec frontend npm run type-check

lint-backend: ## Run PHP CS Fixer
	docker compose exec backend ./vendor/bin/pint

lint-frontend: ## Run ESLint
	docker compose exec frontend npm run lint

# ─── Status ──────────────────────────────────────────────────────────────────
status: ## Show test status and progress
	@echo ""
	@echo "$(GREEN)=== StockFlow Status ===$(RESET)"
	@echo ""
	@echo "$(YELLOW)Backend Tests:$(RESET)"
	@docker compose exec backend php artisan test --parallel 2>&1 | tail -5
	@echo ""
	@echo "$(YELLOW)Frontend Type Check:$(RESET)"
	@docker compose exec frontend npm run type-check 2>&1 | tail -3
	@echo ""
	@echo "$(YELLOW)Current Progress:$(RESET)"
	@grep "Active Phase\|Next Task\|Last Completed" docs/PROGRESS.md | head -5
	@echo ""

# ─── Logs ────────────────────────────────────────────────────────────────────
logs: ## Show all container logs
	docker compose logs -f

logs-backend: ## Show backend logs only
	docker compose logs -f backend

logs-queue: ## Show queue worker logs
	docker compose logs -f queue

# ─── Utilities ───────────────────────────────────────────────────────────────
ide-helper: ## Generate Laravel IDE helper files
	docker compose exec backend php artisan ide-helper:generate
	docker compose exec backend php artisan ide-helper:models -W

routes: ## List all API routes
	docker compose exec backend php artisan route:list --path=api

cache-clear: ## Clear all Laravel caches
	docker compose exec backend php artisan optimize:clear

queue-work: ## Start queue worker in foreground
	docker compose exec backend php artisan queue:work

horizon: ## Start Laravel Horizon dashboard
	docker compose exec backend php artisan horizon

tinker: ## Laravel Tinker REPL
	docker compose exec backend php artisan tinker

# ─── Setup ───────────────────────────────────────────────────────────────────
setup: ## First-time project setup
	@echo "$(GREEN)Setting up StockFlow...$(RESET)"
	cp backend/.env.example backend/.env
	cp frontend/.env.example frontend/.env.local
	docker compose build
	docker compose up -d
	docker compose exec backend composer install
	docker compose exec backend php artisan key:generate
	docker compose exec backend php artisan migrate --seed
	docker compose exec frontend npm install
	@echo "$(GREEN)✓ Setup complete! Visit http://localhost$(RESET)"
