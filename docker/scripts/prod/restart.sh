#!/usr/bin/env bash
set -Eeuo pipefail
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
COMPOSE=(docker compose -f "$ROOT_DIR/docker-compose.prod.yml")
"${COMPOSE[@]}" config --quiet
"${COMPOSE[@]}" build --pull --no-cache app queue-worker schedule-worker nginx
# Abort if the safety export fails. Leave the database container running.
bash "$ROOT_DIR/docker/scripts/prod/export-db.sh"
"${COMPOSE[@]}" up -d --force-recreate --no-deps app queue-worker schedule-worker nginx
