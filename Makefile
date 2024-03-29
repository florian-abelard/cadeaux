#------------------------------------------------------------------------------
# Main Makefile
#------------------------------------------------------------------------------

USER_ID=$(shell id -u)
GROUP_ID=$(shell id -g)
ROOT_PATH=$(shell pwd)
DOCKER_COMPOSE_FILE?=./docker-compose.yml
DOCKER_COMPOSE_BUILDER_FILE?=./docker/docker-compose-builder.yml

export USER_ID
export GROUP_ID
export ROOT_PATH

#------------------------------------------------------------------------------

include .env
export $(shell sed 's/=.*//' .env)

ifneq (,$(wildcard application/.env))
	include application/.env
	export $(shell sed 's/=.*//' application/.env)
endif

ifneq (,$(wildcard application/.env.local))
	include application/.env.local
	export $(shell sed 's/=.*//' application/.env.local)
endif

#------------------------------------------------------------------------------

include makefiles/*.mk

#------------------------------------------------------------------------------

init: composer-install npm-install webpack-build ## install project dependencies

up: up-app db-wait-for db-init webpack-watch ## up application

bash-web: ## open a bash session in the web container
	docker-compose -f ${DOCKER_COMPOSE_FILE} exec web /bin/sh

bash-php: ## open a bash session in the php-fpm container
	docker-compose -f ${DOCKER_COMPOSE_FILE} exec php /bin/sh

bash-node: ## open a bash session in the node container
	docker-compose -f ${DOCKER_COMPOSE_BUILDER_FILE} run --user ${USER_ID}:${GROUP_ID} node /bin/bash

cache-clear: ## clear symfony cache
	docker-compose -f ${DOCKER_COMPOSE_FILE} exec -T php bin/console cache:clear

#------------------------------------------------------------------------------

.DEFAULT_GOAL := help

help:
	@echo "================================================================================"
	@echo " Flo gift list Makefile"
	@echo "================================================================================"
	@echo
	@perl -e '$(HELP_FUNC)' $(MAKEFILE_LIST)
	@echo "================================================================================"

#------------------------------------------------------------------------------

.PHONY: help
