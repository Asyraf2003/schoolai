#!/usr/bin/env bash
set -euo pipefail
umask 077

db() {
    sudo docker exec -i mustaqbal-school-local-mysql-1 \
        sh -ec 'test "$MYSQL_DATABASE" = schoolai
        export MYSQL_PWD="$MYSQL_PASSWORD"
        exec "$@" -u schoolai' sh "$@"
}

backup() {
    DIR="$HOME/.local/share/schoolai/backups"
    mkdir -p "$DIR"
    OUT=$(mktemp "$DIR/schoolai-$(date +%Y%m%d-%H%M%S)-XXXXXX.sql")
    db mysqldump --single-transaction --no-tablespaces \
        --set-gtid-purged=OFF schoolai > "$OUT"
    echo "BACKUP=$OUT"
}

case "$1" in
    backup)
        backup
        ;;
    import)
        FILE="${FILE:-../almusta2_database.sql}"
        test -s "$FILE"
        test "${REPLACE:-0}" = 1 || {
            echo "Use REPLACE=1 to replace local database"
            exit 1
        }

        if grep -Eiq '^[[:space:]]*(CREATE[[:space:]]+(DATABASE|SCHEMA)|USE[[:space:]])' "$FILE"; then
            echo "SQL contains database selection directives; import stopped."
            exit 1
        fi

        systemctl --user stop mustaqbal-school-web.service
        backup

        db mysql -e 'DROP DATABASE schoolai; CREATE DATABASE schoolai;'
        db mysql --binary-mode --local-infile=0 schoolai < "$FILE"

        db mysql schoolai -e 'SHOW TABLES;'
        echo "DB_IMPORT=PASS"
        ;;
esac
