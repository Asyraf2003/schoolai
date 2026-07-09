SHELL := /bin/bash

APP_NAME ?= schoolai-cpanel-app
DEPLOY_DIR ?= deploy-package
NEXT_ID := $(shell mkdir -p "$(DEPLOY_DIR)"; n=1; while [ -e "$(DEPLOY_DIR)/$(APP_NAME)-$$(printf "%03d" $$n).zip" ]; do n=$$((n+1)); done; printf "%03d" $$n)
ZIP_FILE := $(DEPLOY_DIR)/$(APP_NAME)-$(NEXT_ID).zip
STAGE_DIR := /tmp/$(APP_NAME)-deploy-$(NEXT_ID)

.PHONY: deploy
deploy:
	@set -euo pipefail; \
	echo "==> Install fresh frontend dependencies"; \
	npm ci; \
	echo "==> Build Vite assets"; \
	npm run build; \
	echo "==> Refresh Laravel caches"; \
	php artisan config:clear; \
	php artisan route:clear; \
	php artisan view:clear; \
	php artisan config:cache; \
	php artisan route:cache; \
	php artisan view:cache; \
	echo "==> Stage deploy files: $(STAGE_DIR)"; \
	rm -rf "$(STAGE_DIR)"; \
	mkdir -p "$(STAGE_DIR)" "$(DEPLOY_DIR)"; \
	rsync -a ./ "$(STAGE_DIR)/" \
		--exclude ".git/" \
		--exclude ".github/" \
		--exclude ".agents/" \
		--exclude ".codex/" \
		--exclude "node_modules/" \
		--exclude "vendor/" \
		--exclude "tests/" \
		--exclude "deploy-package/" \
		--exclude "README.md" \
		--exclude "Makefile" \
		--exclude "phpunit.xml" \
		--exclude "boost.json" \
		--exclude "package.json" \
		--exclude "package-lock.json" \
		--exclude "vite.config.js" \
		--exclude "resources/css/" \
		--exclude "resources/js/" \
		--exclude "sai.sh" \
		--exclude "sai.ssh" \
		--exclude "storage/logs/*.log" \
		--exclude "storage/framework/cache/data/*" \
		--exclude "storage/framework/sessions/*" \
		--exclude "storage/framework/testing/*" \
		--exclude "storage/framework/views/*.php"; \
	echo "==> Install production Composer dependencies in stage"; \
	cd "$(STAGE_DIR)" && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist; \
	echo "==> Create zip: $(ZIP_FILE)"; \
	cd "$(STAGE_DIR)" && zip -qr "$(abspath $(ZIP_FILE))" .; \
	rm -rf "$(STAGE_DIR)"; \
	php artisan config:clear; \
	php artisan route:clear; \
	php artisan view:clear; \
	echo "==> Done: $(ZIP_FILE)"
