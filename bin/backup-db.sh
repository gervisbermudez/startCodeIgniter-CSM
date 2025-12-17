#!/bin/bash

# Script para crear backup de la base de datos
# Uso: ./bin/backup-db.sh

echo "📦 Creando backup de la base de datos..."

# Ruta del archivo de backup
BACKUP_FILE="application/database/start.sql"

# Crear backup
docker exec ci_mysql57 mysqldump -u root -proot \
  --single-transaction \
  --quick \
  start_cms_db > "$BACKUP_FILE"

# Verificar que se creó correctamente
if [ -f "$BACKUP_FILE" ]; then
  SIZE=$(ls -lh "$BACKUP_FILE" | awk '{print $5}')
  echo "✅ Backup creado exitosamente: $BACKUP_FILE ($SIZE)"
else
  echo "❌ Error al crear el backup"
  exit 1
fi
