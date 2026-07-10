#!/usr/bin/env bash

set -Eeuo pipefail

zip_file="${1:-}"
app_dir="${2:-schoolai}"
public_dir="${3:-public_html}"

if [[ -z "$zip_file" || ! -f "$zip_file" ]]; then
    echo "ERROR: ZIP deployment tidak ditemukan: $zip_file" >&2
    exit 1
fi

listing_file="$(mktemp "${TMPDIR:-/tmp}/schoolai-zip-list.XXXXXX")"
index_file="$(mktemp "${TMPDIR:-/tmp}/schoolai-index.XXXXXX")"
deploy_file="$(mktemp "${TMPDIR:-/tmp}/schoolai-deploy-once.XXXXXX")"

cleanup() {
    rm -f "$listing_file" "$index_file" "$deploy_file"
}
trap cleanup EXIT

unzip -tq "$zip_file" >/dev/null
unzip -Z1 "$zip_file" > "$listing_file"

failures=0

fail() {
    echo "FAIL: $1" >&2
    failures=$((failures + 1))
}

require_entry() {
    local entry="$1"
    if ! grep -Fxq "$entry" "$listing_file"; then
        fail "file wajib tidak ada di ZIP: $entry"
    fi
}

top_levels="$(awk -F/ 'NF {print $1}' "$listing_file" | sort -u | paste -sd ' ' -)"
expected_top_levels="$(printf '%s\n%s\n' "$app_dir" "$public_dir" | sort -u | paste -sd ' ' -)"

if [[ "$top_levels" != "$expected_top_levels" ]]; then
    fail "top-level ZIP harus tepat '$expected_top_levels', ditemukan '$top_levels'"
fi

require_entry "$app_dir/artisan"
require_entry "$app_dir/vendor/autoload.php"
require_entry "$app_dir/.env.example"
require_entry "$app_dir/.env.production.example"
require_entry "$app_dir/.public-path"
require_entry "$public_dir/index.php"
require_entry "$public_dir/.htaccess"
require_entry "$public_dir/build/manifest.json"
require_entry "$public_dir/deploy_once.php"

while IFS= read -r entry; do
    case "$entry" in
        "$app_dir/.env.example"|"$app_dir/.env.production.example")
            ;;
        "$app_dir/.env"|"$app_dir/.env."*)
            fail "environment rahasia ikut ZIP: $entry"
            ;;
    esac

    case "$entry" in
        "$app_dir/.git/"*|"$app_dir/.github/"*|"$app_dir/node_modules/"*|"$app_dir/tests/"*)
            fail "folder development ikut ZIP: $entry"
            ;;
        "$app_dir/public/"*|"$app_dir/resources/css/"*|"$app_dir/resources/js/"*)
            fail "source/public duplikat ikut root Laravel: $entry"
            ;;
        "$app_dir/database/"*.sqlite|"$app_dir/database/"*.sqlite-shm|"$app_dir/database/"*.sqlite-wal)
            fail "database lokal ikut ZIP: $entry"
            ;;
        "$app_dir/storage/app/public/"?*|"$app_dir/storage/app/private/"?*)
            fail "media lokal ikut ZIP: $entry"
            ;;
        "$app_dir/bootstrap/cache/config.php"|"$app_dir/bootstrap/cache/events.php"|"$app_dir/bootstrap/cache/routes"*.php)
            fail "cache environment lokal ikut ZIP: $entry"
            ;;
        "$public_dir/hot"|"$public_dir/storage"|"$public_dir/storage/"*)
            fail "artefak/symlink lokal ikut public_html: $entry"
            ;;
    esac
done < "$listing_file"

unzip -p "$zip_file" "$public_dir/index.php" > "$index_file"
unzip -p "$zip_file" "$public_dir/deploy_once.php" > "$deploy_file"

if grep -Fq "__APP_DIR_NAME__" "$index_file" "$deploy_file"; then
    fail "placeholder APP_DIR_NAME belum diganti"
fi

if grep -Fq "__DEPLOY_TOKEN_HASH__" "$deploy_file"; then
    fail "placeholder token belum diganti"
fi

if ! grep -Fq "usePublicPath(__DIR__)" "$index_file"; then
    fail "index.php belum mengatur public path ke public_html"
fi

if ! grep -Eq "[a-f0-9]{64}" "$deploy_file"; then
    fail "hash token sekali pakai tidak ditemukan"
fi

public_path_marker="$(unzip -p "$zip_file" "$app_dir/.public-path")"
if [[ "$public_path_marker" != "../$public_dir" ]]; then
    fail ".public-path tidak menunjuk ke ../$public_dir"
fi

if (( failures > 0 )); then
    echo "ZIP verification failed with $failures problem(s)." >&2
    exit 1
fi

echo "OK: ZIP structure, secrets, caches, media, vendor, and Vite manifest verified."

