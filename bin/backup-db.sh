#!/bin/bash

# Dump host MySQL (Compose does not run a DB container).
# Usage: ./bin/backup-db.sh

set -e
cd "$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"

if [ -f .env ]; then
    set -a
    # shellcheck disable=SC1091
    . ./.env
    set +a
fi

DB_HOST="${DATABASE_HOSTNAME:-127.0.0.1}"
if [ "$DB_HOST" = "host.docker.internal" ]; then
    DB_HOST="127.0.0.1"
fi
DB_NAME="${DATABASE_DATABASE:-start_cms_db}"
DB_USER="${DATABASE_USER:-ci_user}"
DB_PASS="${DATABASE_PASSWORD:-ci_pass}"

mkdir -p backups
BACKUP_FILE="backups/manual_$(date +%Y%m%d%H%M%S).sql"

echo "Dumping ${DB_NAME} from ${DB_HOST} → ${BACKUP_FILE}"

mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" \
    --single-transaction \
    --quick \
    "$DB_NAME" > "$BACKUP_FILE"

SIZE=$(ls -lh "$BACKUP_FILE" | awk '{print $5}')
echo "Backup created: $BACKUP_FILE ($SIZE)"
