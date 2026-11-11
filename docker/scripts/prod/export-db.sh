#!/usr/bin/env bash
# Dumps the production database to backups/ and removes dumps older than KEEP_DAYS.
set -euo pipefail
umask 077 # dumps contain personal data, readable by owner only

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$ROOT_DIR/backups}"
KEEP_DAYS="${KEEP_DAYS:-14}"
COMPOSE=(docker compose -f "$ROOT_DIR/docker-compose.prod.yml")
BACKUP_FILE="$BACKUP_DIR/backup_prod_$(date +%Y%m%d_%H%M%S).sql.gz"
mkdir -p "$BACKUP_DIR"

# Same credentials the running app uses (db container env may be outdated)
db_config() {
    "${COMPOSE[@]}" exec -T app php artisan tinker --execute="echo config('database.connections.'.config('database.default').'.$1');"
}
DB_USER="$(db_config username)"
DB_NAME="$(db_config database)"
MYSQL_PWD="$(db_config password)"
export MYSQL_PWD # passed by name below, never visible in process list

# Write to .part first, so a failed dump never looks like a finished backup
trap 'rm -f "$BACKUP_FILE.part"' EXIT
"${COMPOSE[@]}" exec -T -e MYSQL_PWD db mariadb-dump --user="$DB_USER" \
    --single-transaction --routines --events --triggers --databases "$DB_NAME" \
    | gzip > "$BACKUP_FILE.part"

# Fails on a corrupted archive or a dump without tables
gzip -dc "$BACKUP_FILE.part" | grep -c '^CREATE TABLE' > /dev/null
mv "$BACKUP_FILE.part" "$BACKUP_FILE"
echo "Backup saved: $BACKUP_FILE"

find "$BACKUP_DIR" -name 'backup_prod_*.sql.gz' -mtime +"$KEEP_DAYS" -delete
