# StockFlow Makefile
# Usage: make <command>

.PHONY: help up down build restart test test-backend test-frontend migrate migrate-test seed fresh shell shell-frontend status logs logs-backend logs-queue type-check lint-backend lint-frontend routes cache-clear queue-work tinker

COMPOSE = docker compose

help: ## Show available commands
	@echo ""
	@echo "StockFlow - Available Commands"
	@echo "--------------------------------"
	@awk 'BEGIN {FS = ":.*?## "}; /^[a-zA-Z_-]+:.*?## / {printf "  %-22s %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ""

up: ## Start all containers
	$(COMPOSE) up -d
	@echo "StockFlow running at http://localhost"

down: ## Stop all containers
	$(COMPOSE) down

build: ## Rebuild all containers
	$(COMPOSE) build --no-cache

restart: ## Restart all containers
	$(COMPOSE) restart

migrate: ## Run database migrations
	$(COMPOSE) exec backend php artisan migrate --force

migrate-test: ## Run migrations on the test database
	$(COMPOSE) exec backend php artisan migrate --env=testing --force

seed: ## Run database seeders
	$(COMPOSE) exec backend php artisan db:seed --force

fresh: ## Fresh migration plus seed, destroying local data
	$(COMPOSE) exec backend php artisan migrate:fresh --seed --force

shell: ## Open a shell in the backend container
	$(COMPOSE) exec backend sh

shell-frontend: ## Open a shell in the frontend container
	$(COMPOSE) exec frontend sh

test: test-backend test-frontend ## Run backend and frontend tests

test-backend: ## Run backend tests
	$(COMPOSE) exec backend php artisan test

test-frontend: ## Run frontend tests
	$(COMPOSE) exec frontend npm run test:ci

type-check: ## Run frontend TypeScript type check
	$(COMPOSE) exec frontend npm run type-check

lint-backend: ## Check backend formatting with Pint
	$(COMPOSE) exec backend ./vendor/bin/pint --test

lint-frontend: ## Run frontend ESLint
	$(COMPOSE) exec frontend npm run lint

status: ## Show container status and core checks
	@echo ""
	@echo "=== Containers ==="
	$(COMPOSE) ps
	@echo ""
	@echo "=== Backend Tests ==="
	$(COMPOSE) exec -T backend php artisan test
	@echo ""
	@echo "=== Frontend Type Check ==="
	$(COMPOSE) exec -T frontend npm run type-check
	@echo ""
	@echo "=== API Routes ==="
	$(COMPOSE) exec -T backend php artisan route:list --path=api

logs: ## Follow all container logs
	$(COMPOSE) logs -f

logs-backend: ## Follow backend logs
	$(COMPOSE) logs -f backend

logs-queue: ## Follow queue worker logs
	$(COMPOSE) logs -f queue

routes: ## List API routes
	$(COMPOSE) exec backend php artisan route:list --path=api

cache-clear: ## Clear Laravel caches
	$(COMPOSE) exec backend php artisan optimize:clear

queue-work: ## Start a queue worker in the backend container
	$(COMPOSE) exec backend php artisan queue:work

tinker: ## Open Laravel Tinker
	$(COMPOSE) exec backend php artisan tinker
