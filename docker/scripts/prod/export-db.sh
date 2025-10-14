#!/bin/bash
set -e

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
ENV_FILE="${ROOT_DIR}/.env"
BACKUP_DIR="${ROOT_DIR}/backups"
mkdir -p "$BACKUP_DIR"

DB_USERNAME=$(grep '^DB_USERNAME=' "$ENV_FILE" | cut -d '=' -f2-)
DB_PASSWORD=$(grep '^DB_PASSWORD=' "$ENV_FILE" | cut -d '=' -f2-)
DB_DATABASE=$(grep '^DB_DATABASE=' "$ENV_FILE" | cut -d '=' -f2-)

if [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ] || [ -z "$DB_DATABASE" ]; then
  echo "Missing required variables DB_USERNAME, DB_PASSWORD or DB_DATABASE in .env"
  exit 1
fi

BACKUP_FILE="${BACKUP_DIR}/backup_prod_$(date +%Y%m%d_%H%M%S).sql.gz"

docker compose -f "${ROOT_DIR}/docker-compose.prod.yml" exec db sh -c \
  "mariadb-dump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}" | gzip > "$BACKUP_FILE"

if [ -s "$BACKUP_FILE" ]; then
  echo "Backup saved: $BACKUP_FILE"
else
  echo "Error: backup file is empty."
  exit 1
fi

find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +7 -exec rm -f {} \;
