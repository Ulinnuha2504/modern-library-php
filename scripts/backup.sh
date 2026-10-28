#!/bin/bash
BACKUP_DIR="/var/www/perpustakaan/storage/backups"
ENV_FILE="/var/www/perpustakaan/.env"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RETENTION_DAYS=30
mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"
DB_HOST=$(grep -E '^DB_HOST=' "$ENV_FILE" | cut -d '=' -f2 | tr -d '"\r')
DB_PORT=$(grep -E '^DB_PORT=' "$ENV_FILE" | cut -d '=' -f2 | tr -d '"\r')
DB_NAME=$(grep -E '^DB_DATABASE=' "$ENV_FILE" | cut -d '=' -f2 | tr -d '"\r')
DB_USER=$(grep -E '^DB_USERNAME=' "$ENV_FILE" | cut -d '=' -f2 | tr -d '"\r')
DB_PASS=$(grep -E '^DB_PASSWORD=' "$ENV_FILE" | cut -d '=' -f2 | tr -d '"\r')
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_NAME=${DB_NAME:-perpustakaan}
DB_USER=${DB_USER:-root}
BACKUP_FILE="${BACKUP_DIR}/${DB_NAME}_backup_${TIMESTAMP}.sql.gz"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Memulai backup ${DB_NAME}..."
if [ -z "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" --single-transaction --quick --routines --triggers "$DB_NAME" | gzip > "$BACKUP_FILE"
else
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" --single-transaction --quick --routines --triggers "$DB_NAME" | gzip > "$BACKUP_FILE"
fi
if [ $? -eq 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Backup sukses: ${BACKUP_FILE}"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ERROR: mysqldump gagal!" >&2
    exit 1
fi
find "$BACKUP_DIR" -type f -name "${DB_NAME}_backup_*.sql.gz" -mtime +${RETENTION_DAYS} -exec rm -f {} \;
