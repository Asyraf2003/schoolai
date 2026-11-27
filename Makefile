SHELL := /bin/bash

APP_NAME ?= schoolai-cpanel
APP_DIR_NAME ?= schoolai
PUBLIC_DIR_NAME ?= public_html
DEPLOY_DIR ?= deploy-package
SITE_URL ?= https://almustaqbal.sch.id

.PHONY: deploy
deploy:
	@APP_NAME="$(APP_NAME)" \
	APP_DIR_NAME="$(APP_DIR_NAME)" \
	PUBLIC_DIR_NAME="$(PUBLIC_DIR_NAME)" \
	DEPLOY_DIR="$(DEPLOY_DIR)" \
	SITE_URL="$(SITE_URL)" \
	bash scripts/build-cpanel-package.sh


# Local development targets
.DEFAULT_GOAL := help

.PHONY: on off status migrate test verify logs help build lint

off status migrate test verify logs:
	@school $@

build:
	@npm run build

lint:
	@vendor/bin/pint --test

help:
	@school help
	@echo "make [on|off|status|migrate|test|verify|logs|build|lint|routes|db-backup|db-import|push|deploy]"

.PHONY: push

push:
	@test -z "$$(git status --porcelain)" || { echo "Commit changes first"; exit 1; }
	@git push -u origin HEAD

.PHONY: on
on:
	@school on
	@echo "URL: http://127.0.0.1:8001"

.PHONY: routes db-backup db-import
FILE ?= ../almusta2_database.sql
export FILE REPLACE

routes:
	@php artisan route:list --except-vendor

db-backup:
	@bash scripts/local-db.sh backup

db-import:
	@bash scripts/local-db.sh import
