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

set_env_value() {
    local file="$1"
    local key="$2"
    local value="$3"

    php -r '
        $file = $argv[1];
        $key = $argv[2];
        $value = $argv[3];
        $contents = file_get_contents($file);

        if ($contents === false) {
            fwrite(STDERR, "Unable to read staged environment file.\n");
            exit(1);
        }

        $pattern = "/^".preg_quote($key, "/")."=.*$/m";
        $line = $key."=".$value;

        if (preg_match($pattern, $contents) === 1) {
            $contents = preg_replace($pattern, $line, $contents, 1);
        } else {
            $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        if ($contents === null || file_put_contents($file, $contents, LOCK_EX) === false) {
            fwrite(STDERR, "Unable to write staged environment file.\n");
            exit(1);
        }
    ' "$file" "$key" "$value"
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
    .env \
    .env.example \
    package.json \
    package-lock.json \
    public/index.php \
    public/.htaccess \
    deploy/cpanel/index.php.template \
    deploy/cpanel/clear.php.template \
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

# Start from the developer's local .env so existing AWS/Google credentials and
# other project-specific values are carried into the package. Only values that
# must differ in production are overwritten below.
cp .env "$app_stage/.env"
cp .env.example "$app_stage/.env.example"
cp deploy/cpanel/.env.production.example "$app_stage/.env.production.example"

echo "==> Convert staged .env to production"
set_env_value "$app_stage/.env" "APP_NAME" "School"
set_env_value "$app_stage/.env" "APP_ENV" "production"
set_env_value "$app_stage/.env" "APP_KEY" "base64:tx01Hkpu3XouFanMD5F/m685fe7YJuxJ8vDQX3Wi8o0="
set_env_value "$app_stage/.env" "APP_TIMEZONE" "Asia/Jakarta"
set_env_value "$app_stage/.env" "APP_DEBUG" "false"
set_env_value "$app_stage/.env" "APP_URL" "$SITE_URL"
set_env_value "$app_stage/.env" "APP_LOCALE" "en"
set_env_value "$app_stage/.env" "APP_FALLBACK_LOCALE" "id"
set_env_value "$app_stage/.env" "APP_FAKER_LOCALE" "en_US"
set_env_value "$app_stage/.env" "APP_MAINTENANCE_DRIVER" "file"
set_env_value "$app_stage/.env" "BCRYPT_ROUNDS" "12"
set_env_value "$app_stage/.env" "LOG_CHANNEL" "stack"
set_env_value "$app_stage/.env" "LOG_STACK" "single"
set_env_value "$app_stage/.env" "LOG_DEPRECATIONS_CHANNEL" "null"
set_env_value "$app_stage/.env" "LOG_LEVEL" "debug"
set_env_value "$app_stage/.env" "DB_CONNECTION" "mysql"
set_env_value "$app_stage/.env" "DB_HOST" "127.0.0.1"
set_env_value "$app_stage/.env" "DB_PORT" "3306"
set_env_value "$app_stage/.env" "DB_DATABASE" "almusta2_database"
set_env_value "$app_stage/.env" "DB_USERNAME" "almusta2_database"
set_env_value "$app_stage/.env" "DB_PASSWORD" "almusta2_database"
set_env_value "$app_stage/.env" "DB_TIMEZONE" "+07:00"
set_env_value "$app_stage/.env" "SESSION_SECURE_COOKIE" "true"
set_env_value "$app_stage/.env" "GOOGLE_REDIRECT_URI" "$SITE_URL/auth/google/callback"
chmod 0600 "$app_stage/.env"

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

clear_token="$(php -r 'echo bin2hex(random_bytes(32));')"
clear_token_hash="$(php -r 'echo hash("sha256", $argv[1]);' "$clear_token")"

sed \
    -e "s/__APP_DIR_NAME__/$APP_DIR_NAME/g" \
    -e "s/__DEPLOY_TOKEN_HASH__/$clear_token_hash/g" \
    deploy/cpanel/clear.php.template > "$public_stage/clear.php"

php -l "$public_stage/index.php" >/dev/null
php -l "$public_stage/clear.php" >/dev/null

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
# after the packaged production .env is in its final location.
rm -f \
    "$app_stage/bootstrap/cache/config.php" \
    "$app_stage/bootstrap/cache/events.php" \
    "$app_stage"/bootstrap/cache/routes*.php

echo "==> Normalize shared-hosting permissions"
find "$app_stage" "$public_stage" -type d -exec chmod 0755 {} +
find "$app_stage" "$public_stage" -type f -exec chmod 0644 {} +
chmod 0755 "$app_stage/artisan"
chmod 0600 "$app_stage/.env"

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
    "$PUBLIC_DIR_NAME" \
    "$SITE_URL"

{
    echo "SchoolAI cPanel deployment"
    echo "ZIP: $(basename "$zip_file")"
    echo "Extract the ZIP directly into the cPanel home directory."
    echo "The packaged $APP_DIR_NAME/.env is already converted to production values."
    echo "After extraction, open this one-time maintenance URL:"
    echo "URL: $SITE_URL/clear.php?token=$clear_token"
    echo "clear.php runs optimize:clear, migrate --force, optimize, then deletes itself after success."
} > "$setup_file"
chmod 600 "$zip_file" "$setup_file"

echo "==> Done"
echo "ZIP: $zip_file"
echo "SETUP: $setup_file"
echo "Keep both files private; the ZIP contains production credentials and the setup file contains the one-time clear.php token."
