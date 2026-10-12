#!/usr/bin/env bash
set -euo pipefail

# Local-only preview asset. No database, R2 overwrite, or private credentials.
if [[ $# -ne 1 || ! -f "$1" ]]; then
    printf 'Usage: bash scripts/set-footer-map.sh /path/to/school-map.png\n' >&2
    exit 2
fi

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
target="$root/public/media/footer/school-map.webp"
mkdir -p "$(dirname "$target")"

if command -v magick >/dev/null 2>&1; then
    magick "$1" -auto-orient -strip -resize '1600x1600>' -quality 82 "$target"
elif command -v ffmpeg >/dev/null 2>&1; then
    ffmpeg -hide_banner -loglevel error -y -i "$1" \
        -vf 'scale=min(1600\,iw):-2' -frames:v 1 -c:v libwebp -quality 82 "$target"
else
    printf 'Install ImageMagick or ffmpeg, then run again.\n' >&2
    exit 1
fi

printf 'LOCAL_MAP_READY=%s\n' "$target"
printf 'Preview URL: /media/footer/school-map.webp\n'
printf 'Reload the page, then hover Maps. This does not upload to R2.\n'
