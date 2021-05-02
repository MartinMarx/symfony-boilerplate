include .env
-include .env.local

docker-compose:=:
php_cli := $(shell which php) -d memory_limit=-1
composer := $(shell which composer)
console := $(php_cli) bin/console
server_start := symfony server:start -d && symfony open:local
server_stop := symfony server:stop

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

install:
	composer install
	yarn install --force
.PHONY: install

grunt: ## grunt - Generate minified javascript and css files
	grunt --gruntfile ./public/Gruntfile.js
.PHONY: grunt

migrations:
	$(console) make:migration
	$(console) doctrine:migration:migrate -n
.PHONY: migrations

database: ## database - create database and load migrations
	$(console) --env $(APP_ENV) doctrine:database:create --if-not-exists --no-interaction
	$(console) --env $(APP_ENV) doctrine:migrations:migrate --no-interaction
	$(console) --env $(APP_ENV) hautelook:fixtures:load --no-interaction
.PHONY: database

fixtures: ## fixtures - Generate data fixtures
	$(console) --env $(APP_ENV) hautelook:fixtures:load --no-interaction
.PHONY: fixtures

cache-clear:
	$(console) --env $(APP_ENV) cache:clear --no-warmup
.PHONY: cache-clear

cache-warmup: cache-clear
	$(console) --env $(APP_ENV) cache:warmup
.PHONY: cache-warmup

fixcs: ## fixcs - Fix coding standards
	$(php_cli) vendor/bin/php-cs-fixer fix
.PHONY: fixcs

phpstan: ## phpstan - Static analysis
	$(php_cli) vendor/bin/phpstan analyse
.PHONY: phpstan

phpmetrics: ## phpmetrics - Code quality analysis
	$(php_cli) vendor/bin/phpmetrics --report-html=var/phpmetrics src && open var/phpmetrics/index.html
.PHONY: phpmetrics

security: ## security - Check symfony dependencies security
	symfony check:security
.PHONY: security

test: ## test - Run testsuite
	$(php_cli) bin/phpunit
.PHONY: phpunit

lint-composer: var ## Checks composer.json syntax
	$(composer) validate
.PHONY: lint-composer

lint-yaml: vendor ## Checks yaml syntax
	$(console) lint:yaml config --parse-tags
.PHONY: lint-yaml

lint-doctrine: vendor ## Checks doctrine mapping
	$(console) doctrine:schema:validate --skip-sync
.PHONY: lint-doctrine

lint-twig: vendor ## Checks yaml syntax
	$(console) lint:twig templates
.PHONY: lint-twig

check: lint-composer lint-yaml lint-doctrine lint-twig security fixcs phpstan test
.PHONY: check
