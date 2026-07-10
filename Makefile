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

