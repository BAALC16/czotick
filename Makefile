.PHONY: help build up down restart logs shell composer npm migrate seed fresh install

help: ## Afficher cette aide
	@echo "Commandes disponibles:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Construire les images Docker
	docker-compose build

up: ## Démarrer les conteneurs
	docker-compose up -d

down: ## Arrêter les conteneurs
	docker-compose down

restart: ## Redémarrer les conteneurs
	docker-compose restart

logs: ## Voir les logs
	docker-compose logs -f

shell: ## Accéder au shell du conteneur app
	docker-compose exec app bash

composer: ## Installer les dépendances Composer
	docker-compose exec app composer install

npm: ## Installer les dépendances NPM
	docker-compose exec app npm install

migrate: ## Exécuter les migrations
	docker-compose exec app php artisan migrate

seed: ## Exécuter les seeders
	docker-compose exec app php artisan db:seed

fresh: ## Réinitialiser la base de données
	docker-compose exec app php artisan migrate:fresh --seed

install: build up composer npm migrate ## Installation complète
	@echo "✅ Installation terminée!"
	@echo "🌐 Application disponible sur http://localhost:8080"

clean: ## Nettoyer les conteneurs et volumes
	docker-compose down -v

cache-clear: ## Vider tous les caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

optimize: ## Optimiser l'application
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

queue: ## Démarrer le worker de queue
	docker-compose exec app php artisan queue:work

tinker: ## Ouvrir Tinker
	docker-compose exec app php artisan tinker

mysql: ## Accéder à MySQL
	docker-compose exec mysql mysql -u root -proot

redis: ## Accéder à Redis CLI
	docker-compose exec redis redis-cli

