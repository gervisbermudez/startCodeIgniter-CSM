#!/bin/bash

# Script para verificar si las tablas de analytics existen
# Uso: ./check-analytics-db.sh [database_name] [username]

DB_NAME="${1:-startcms}"
DB_USER="${2:-root}"

echo "========================================="
echo "Verificación de Base de Datos Analytics"
echo "========================================="
echo ""
echo "Base de datos: $DB_NAME"
echo "Usuario: $DB_USER"
echo ""

# Solicitar contraseña
read -sp "Contraseña MySQL: " DB_PASS
echo ""
echo ""

# Verificar tablas
echo "Verificando tablas de analytics..."
echo ""

mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$DB_NAME' AND TABLE_NAME = 'user_tracking') 
        THEN '✓' 
        ELSE '✗' 
    END as user_tracking,
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$DB_NAME' AND TABLE_NAME = 'user_sessions') 
        THEN '✓' 
        ELSE '✗' 
    END as user_sessions,
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$DB_NAME' AND TABLE_NAME = 'user_tracking_events') 
        THEN '✓' 
        ELSE '✗' 
    END as user_tracking_events,
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$DB_NAME' AND TABLE_NAME = 'user_tracking_daily_stats') 
        THEN '✓' 
        ELSE '✗' 
    END as user_tracking_daily_stats;
"

echo ""
echo "Verificando columnas en user_tracking..."
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
SHOW COLUMNS FROM user_tracking;
" 2>/dev/null || echo "⚠️  La tabla user_tracking no existe o no tiene las columnas necesarias"

echo ""
echo "========================================="
echo "Instrucciones:"
echo "========================================="
echo ""
echo "Si ves ✗ en alguna tabla, necesitas ejecutar la migración:"
echo ""
echo "mysql -u $DB_USER -p $DB_NAME < application/database/migrations/001_improve_user_tracking.sql"
echo ""
echo "O usa el script de instalación automática:"
echo "./install-analytics.sh"
echo ""
