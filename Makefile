dc := docker-compose
de := $(dc) exec

.DEFAULT_GOAL := help
.PHONY: help build dev install app stop

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build: ## Build docker container
	$(dc) build

dev: ## Launch docker env
	$(dc) up -d

install: ## Install composer dependencies
	$(de) app composer install --no-interaction

app: ## Allows you to enter the PHP container
	$(de) app bash

stop: ## Stop docker container
	$(dc) stop
