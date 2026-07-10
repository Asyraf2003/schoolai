#!/usr/bin/env bash

set -Eeuo pipefail
umask 022

ROOT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

APP_NAME="${APP_NAME:-schoolai-cpanel}"
APP_DIR_NAME="${APP_DIR_NAME:-schoolai}"
PUBLIC_DIR_NAME="${PUBLIC_DIR_NAME:-public_html}"
DEPLOY_DIR="${DEPLOY_DIR:-deploy-package}"
SITE_URL="${SITE_URL:-https://almustaqbal.sch.id}"
SITE_URL="${SITE_URL%/}"

validate_name() {
    local label="$1"
    local value="$2"

    if [[ -z "$value" || "$value" == "." || "$value" == ".." || ! "$value" =~ ^[A-Za-z0-9._-]+$ ]]; then
        echo "ERROR: $label hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus." >&2
        exit 1
    fi
}

validate_name "APP_NAME" "$APP_NAME"
validate_name "APP_DIR_NAME" "$APP_DIR_NAME"
validate_name "PUBLIC_DIR_NAME" "$PUBLIC_DIR_NAME"
validate_name "DEPLOY_DIR" "$DEPLOY_DIR"

for command_name in git npm php composer rsync zip unzip sed; do
    if ! command -v "$command_name" >/dev/null 2>&1; then
        echo "ERROR: command '$command_name' tidak tersedia." >&2
        exit 1
    fi
done

if [[ "$(git rev-parse --is-inside-work-tree 2>/dev/null || true)" != "true" ]]; then
    echo "ERROR: builder harus dijalankan dari checkout Git SchoolAI." >&2
    exit 1
fi

worktree_status="$(git status --porcelain --untracked-files=all)"
if [[ -n "$worktree_status" ]]; then
    echo "ERROR: working tree belum bersih. Commit, pindahkan, atau hapus file berikut sebelum membuat paket:" >&2
    printf '%s\n' "$worktree_status" >&2
    exit 1
fi

for required_file in \
    artisan \
    composer.json \
    composer.lock \
    .env.example \
    package.json \
    package-lock.json \
    public/index.php \
    public/.htaccess \
    deploy/cpanel/index.php.template \
    deploy/cpanel/deploy_once.php.template \
    deploy/cpanel/.env.production.example; do
    if [[ ! -f "$required_file" ]]; then
        echo "ERROR: file wajib tidak ditemukan: $required_file" >&2
        exit 1
    fi
done

mkdir -p "$DEPLOY_DIR"

next_id=1
while [[ -e "$DEPLOY_DIR/$APP_NAME-$(printf '%03d' "$next_id").zip" ]]; do
    next_id=$((next_id + 1))
done

next_id_padded="$(printf '%03d' "$next_id")"
zip_file="$DEPLOY_DIR/$APP_NAME-$next_id_padded.zip"
setup_file="$DEPLOY_DIR/$APP_NAME-$next_id_padded-setup.txt"
stage_dir="$(mktemp -d "${TMPDIR:-/tmp}/$APP_NAME-deploy-$next_id_padded.XXXXXX")"
app_stage="$stage_dir/$APP_DIR_NAME"
public_stage="$stage_dir/$PUBLIC_DIR_NAME"

cleanup() {
    rm -rf "$stage_dir"
}
trap cleanup EXIT

echo "==> Install frontend dependencies"
npm ci

echo "==> Build Vite assets"
npm run build

if [[ ! -f public/build/manifest.json ]]; then
    echo "ERROR: public/build/manifest.json tidak dibuat oleh Vite." >&2
    exit 1
fi

echo "==> Remove local environment caches"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Stage Laravel root: $APP_DIR_NAME/"
mkdir -p "$app_stage" "$public_stage"

rsync -a ./ "$app_stage/" \
    --exclude "/.git/" \
    --exclude "/.github/" \
    --exclude "/.agents/" \
    --exclude "/.claude/" \
    --exclude "/.codex/" \
    --exclude "/.cursor/" \
    --exclude "/.idea/" \
    --exclude "/.nova/" \
    --exclude "/.vscode/" \
    --exclude "/.zed/" \
    --exclude "/.env" \
    --exclude "/.env.*" \
    --exclude "/.public-path" \
    --exclude "/auth.json" \
    --exclude "/node_modules/" \
    --exclude "/vendor/" \
    --exclude "/tests/" \
    --exclude "/deploy/" \
    --exclude "/scripts/" \
    --exclude "/$DEPLOY_DIR/" \
    --exclude "/public/" \
    --exclude "/resources/css/" \
    --exclude "/resources/js/" \
    --exclude "/storage/app/public/***" \
    --exclude "/storage/app/private/***" \
    --exclude "/storage/framework/cache/data/***" \
    --exclude "/storage/framework/sessions/***" \
    --exclude "/storage/framework/testing/***" \
    --exclude "/storage/framework/views/***" \
    --exclude "/storage/logs/***" \
    --exclude "/bootstrap/cache/*.php" \
    --exclude "/database/*.sqlite" \
    --exclude "/database/*.sqlite-shm" \
    --exclude "/database/*.sqlite-wal" \
    --exclude "/.commit_counter" \
    --exclude "/.phpactor.json" \
    --exclude "/.phpunit.cache/" \
    --exclude "/.phpunit.result.cache" \
    --exclude "/_ide_helper.php" \
    --exclude "/boost.json" \
    --exclude "/Makefile" \
    --exclude "/phpunit.xml" \
    --exclude "/package.json" \
    --exclude "/package-lock.json" \
    --exclude "/vite.config.js" \
    --exclude "/sai.sh" \
    --exclude "/sai.ssh"

cp .env.example "$app_stage/.env.example"
cp deploy/cpanel/.env.production.example "$app_stage/.env.production.example"

mkdir -p \
    "$app_stage/bootstrap/cache" \
    "$app_stage/storage/app/private" \
    "$app_stage/storage/app/public" \
    "$app_stage/storage/framework/cache/data" \
    "$app_stage/storage/framework/sessions" \
    "$app_stage/storage/framework/testing" \
    "$app_stage/storage/framework/views" \
    "$app_stage/storage/logs"

printf '%s\n' "../$PUBLIC_DIR_NAME" > "$app_stage/.public-path"

echo "==> Stage public document root: $PUBLIC_DIR_NAME/"
rsync -a public/ "$public_stage/" \
    --exclude "/hot" \
    --exclude "/storage" \
    --exclude "/storage/***"

sed "s/__APP_DIR_NAME__/$APP_DIR_NAME/g" \
    deploy/cpanel/index.php.template > "$public_stage/index.php"

deploy_token="$(php -r 'echo bin2hex(random_bytes(32));')"
deploy_token_hash="$(php -r 'echo hash("sha256", $argv[1]);' "$deploy_token")"

sed \
    -e "s/__APP_DIR_NAME__/$APP_DIR_NAME/g" \
    -e "s/__DEPLOY_TOKEN_HASH__/$deploy_token_hash/g" \
    deploy/cpanel/deploy_once.php.template > "$public_stage/deploy_once.php"

php -l "$public_stage/index.php" >/dev/null
php -l "$public_stage/deploy_once.php" >/dev/null

echo "==> Install production Composer dependencies"
(
    cd "$app_stage"
    COMPOSER_ALLOW_SUPERUSER=1 composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist
)

# Environment-specific caches must only be generated on the hosting server
# after the production .env file has been installed.
rm -f \
    "$app_stage/bootstrap/cache/config.php" \
    "$app_stage/bootstrap/cache/events.php" \
    "$app_stage"/bootstrap/cache/routes*.php

echo "==> Normalize shared-hosting permissions"
find "$app_stage" "$public_stage" -type d -exec chmod 0755 {} +
find "$app_stage" "$public_stage" -type f -exec chmod 0644 {} +
chmod 0755 "$app_stage/artisan"

echo "==> Create ZIP: $zip_file"
zip_file_absolute="$ROOT_DIR/$zip_file"
(
    cd "$stage_dir"
    zip -qr "$zip_file_absolute" "$APP_DIR_NAME" "$PUBLIC_DIR_NAME"
)

echo "==> Verify deployment package"
bash scripts/verify-cpanel-package.sh \
    "$zip_file" \
    "$APP_DIR_NAME" \
    "$PUBLIC_DIR_NAME"

{
    echo "SchoolAI one-time deployment"
    echo "ZIP: $(basename "$zip_file")"
    echo "Extract into the cPanel home directory."
    echo "Place the production .env at: $APP_DIR_NAME/.env"
    echo "Complete the database setup before running the URL below."
    echo "URL: $SITE_URL/deploy_once.php?token=$deploy_token"
    echo "The setup script deletes itself after a successful run."
} > "$setup_file"
chmod 600 "$zip_file" "$setup_file"

echo "==> Done"
echo "ZIP: $zip_file"
echo "SETUP: $setup_file"
echo "Keep the setup file private; it contains the one-time token."
