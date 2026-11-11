#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$ROOT_DIR/backups}"
KEEP_DAYS="${KEEP_DAYS:-14}"
[[ "$KEEP_DAYS" =~ ^[0-9]+$ ]] || { echo 'KEEP_DAYS must be a nonnegative integer' >&2; exit 1; }
mkdir -p "$BACKUP_DIR"
exec 9>"$BACKUP_DIR/.export.lock"
flock -n 9 || { echo 'Another database export is running' >&2; exit 1; }

TMP_FILE="$(mktemp "$BACKUP_DIR/.db-export.XXXXXXXX.sql.gz")"
trap 'rm -f -- "$TMP_FILE"' EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

# Credentials stay inside the container, never interpolated into shell source.
# Do not source Laravel's .env or put a password in command arguments.
docker compose -f "$ROOT_DIR/docker-compose.prod.yml" exec -T db sh -eu -c '
    export MYSQL_PWD="${MYSQL_ROOT_PASSWORD:?MYSQL_ROOT_PASSWORD is missing}"
    exec mariadb-dump --user=root --single-transaction --quick \
        --routines --events --triggers --hex-blob \
        --databases "${MYSQL_DATABASE:?MYSQL_DATABASE is missing}"
' | gzip -c > "$TMP_FILE"

gzip -t "$TMP_FILE"
# A valid gzip may contain no SQL. Require an application table definition.
# Consume the whole stream so pipefail does not mistake SIGPIPE for failure.
gzip -dc "$TMP_FILE" | awk '/^CREATE TABLE / { found=1 } END { exit !found }'

BACKUP_FILE="$BACKUP_DIR/backup_prod_$(date -u +%Y%m%d_%H%M%S).sql.gz"
mv -- "$TMP_FILE" "$BACKUP_FILE"
echo "Backup saved: $BACKUP_FILE"
# Only rotate our own completed exports, after a successful export.
find "$BACKUP_DIR" -maxdepth 1 -type f -name 'backup_prod_*.sql.gz' \
    -mtime +"$KEEP_DAYS" -delete
