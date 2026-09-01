.DEFAULT_GOAL := help
.PHONY: help up down build restart logs shell dbshell fresh seed test pint route-check dev tools clean

DC   := docker compose
EXEC := $(DC) exec app

help: ## Mostra questo elenco
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

up: ## Avvia l'app su http://localhost
	@test -f .env || cp .env.example .env
	$(DC) up -d --build

down: ## Ferma i container
	$(DC) down

build: ## Ricostruisce le immagini da zero
	$(DC) build --no-cache

restart: down up ## Riavvia tutto

logs: ## Segue i log
	$(DC) logs -f

shell: ## Shell nel container app
	$(EXEC) bash

dbshell: ## Client MySQL sul database
	$(DC) exec db mysql -u root -p$${DB_ROOT_PASSWORD:-root} $${DB_DATABASE:-mexam}

fresh: ## Ricrea il database e ripopola i dati di demo
	$(EXEC) php artisan migrate:fresh --seed
	$(EXEC) touch storage/.mexam-seeded

seed: ## Ripopola senza ricreare lo schema
	$(EXEC) php artisan db:seed

test: ## Esegue la suite
	$(EXEC) vendor/bin/phpunit

pint: ## Formatta il codice
	$(EXEC) vendor/bin/pint

pint-check: ## Verifica la formattazione senza modificare
	$(EXEC) vendor/bin/pint --test

route-check: ## Elenca le rotte e verifica che route:cache non esploda
	$(EXEC) php artisan route:list
	$(EXEC) php artisan route:cache
	$(EXEC) php artisan route:clear

dev: ## Avvia Vite in HMR su :5173
	$(DC) --profile dev up node

tools: ## Avvia phpMyAdmin su :8081
	$(DC) --profile tools up -d phpmyadmin

clean: ## Ferma tutto e cancella il volume del database
	$(DC) down -v
