
SAIL := ./vendor/bin/sail

.PHONY: install up down dev build test migrate freshseed routes sail tinker

install: # Serie di comandi che servono per installare cio che è necessario per l'applicazione
	docker run --rm -v "$(CURDIR)":/opt -w /opt laravelsail/php84-composer:latest composer install
	cp -n .env.example .env
	touch database/database.sqlite
	$(SAIL) up -d
	$(SAIL) artisan key:generate
	$(SAIL) artisan migrate --seed
	$(SAIL) npm install
	$(SAIL) npm run build

up:
	$(SAIL) up -d

down:
	$(SAIL) down

dev:
	$(SAIL) npm run dev

build:
	$(SAIL) npm run build

test:
	$(SAIL) artisan test

migrate:
	$(SAIL) artisan migrate

freshseed: # Svuota e riempi le tabelle
	$(SAIL) artisan migrate:fresh --seed

routes: # Mostra tutte le route configurate nell'applicazione
	$(SAIL) artisan route:list

tinker: # Entra in ambiente tinker
	$(SAIL) artisan tinker
