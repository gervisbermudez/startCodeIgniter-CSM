#!/bin/bash

# ========================================
# Script de Instalación - Analytics System v2.0
# ========================================

echo "╔════════════════════════════════════════════════════════╗"
echo "║   Analytics System v2.0 - Instalación Automática      ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "ℹ $1"
}

# Directorio del proyecto
PROJECT_DIR=$(pwd)

echo "Directorio del proyecto: $PROJECT_DIR"
echo ""

# ========================================
# Paso 1: Verificar archivos necesarios
# ========================================
echo "📋 Paso 1: Verificando archivos..."

if [ ! -f "$PROJECT_DIR/application/database/migrations/001_improve_user_tracking.sql" ]; then
    print_error "No se encontró el archivo de migración SQL"
    exit 1
fi

if [ ! -f "$PROJECT_DIR/application/libraries/Track_Visitor_Enhanced.php" ]; then
    print_error "No se encontró la librería Track_Visitor_Enhanced.php"
    exit 1
fi

print_success "Todos los archivos necesarios están presentes"
echo ""

# ========================================
# Paso 2: Copiar archivos JavaScript
# ========================================
echo "📦 Paso 2: Copiando archivos JavaScript..."

# Crear directorios si no existen
mkdir -p "$PROJECT_DIR/public/js"
mkdir -p "$PROJECT_DIR/public/js/components"

# Copiar archivos
if [ -f "$PROJECT_DIR/resources/js/analytics-client.js" ]; then
    cp "$PROJECT_DIR/resources/js/analytics-client.js" "$PROJECT_DIR/public/js/analytics-client.min.js"
    print_success "analytics-client.js copiado"
else
    print_warning "analytics-client.js no encontrado en resources/js/"
fi

if [ -f "$PROJECT_DIR/resources/components/AnalyticsDashboard.js" ]; then
    cp "$PROJECT_DIR/resources/components/AnalyticsDashboard.js" "$PROJECT_DIR/public/js/components/AnalyticsDashboard.js"
    print_success "AnalyticsDashboard.js copiado"
else
    print_warning "AnalyticsDashboard.js no encontrado"
fi

echo ""

# ========================================
# Paso 3: Configuración de Base de Datos
# ========================================
echo "🗄️  Paso 3: Configuración de Base de Datos"
echo ""

print_info "Necesitamos la configuración de tu base de datos..."
echo ""

# Leer configuración de database.php
DB_CONFIG_FILE="$PROJECT_DIR/application/config/database.php"

if [ -f "$DB_CONFIG_FILE" ]; then
    print_info "Leyendo configuración desde database.php..."
    
    # Extraer datos (método simple, podría mejorarse)
    DB_HOST=$(grep -oP "hostname.*=>.*'\K[^']+" "$DB_CONFIG_FILE" | head -1)
    DB_NAME=$(grep -oP "database.*=>.*'\K[^']+" "$DB_CONFIG_FILE" | head -1)
    DB_USER=$(grep -oP "username.*=>.*'\K[^']+" "$DB_CONFIG_FILE" | head -1)
    
    print_info "Base de datos: $DB_NAME"
    print_info "Usuario: $DB_USER"
    print_info "Host: $DB_HOST"
    echo ""
fi

# Preguntar si quiere ejecutar la migración ahora
read -p "¿Deseas ejecutar la migración de base de datos ahora? (s/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo ""
    print_info "Ejecutando migración de base de datos..."
    
    # Pedir contraseña
    read -sp "Contraseña de MySQL: " DB_PASS
    echo ""
    
    # Ejecutar migración
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$PROJECT_DIR/application/database/migrations/001_improve_user_tracking.sql"
    
    if [ $? -eq 0 ]; then
        print_success "Migración ejecutada correctamente"
        echo ""
        
        # Preguntar si quiere migrar datos existentes
        read -p "¿Tienes datos existentes que quieres migrar? (s/n): " -n 1 -r
        echo ""
        
        if [[ $REPLY =~ ^[Ss]$ ]]; then
            print_info "Migrando datos existentes..."
            mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$PROJECT_DIR/application/database/migrations/002_migrate_existing_data.sql"
            
            if [ $? -eq 0 ]; then
                print_success "Datos migrados correctamente"
            else
                print_warning "Hubo un problema al migrar los datos (puedes hacerlo manualmente después)"
            fi
        fi
    else
        print_error "Error al ejecutar la migración"
        print_info "Puedes ejecutarla manualmente con:"
        echo "mysql -u $DB_USER -p $DB_NAME < application/database/migrations/001_improve_user_tracking.sql"
    fi
else
    print_warning "Migración omitida"
    print_info "Recuerda ejecutarla manualmente:"
    echo "mysql -u $DB_USER -p $DB_NAME < application/database/migrations/001_improve_user_tracking.sql"
fi

echo ""

# ========================================
# Paso 4: Verificar configuración
# ========================================
echo "⚙️  Paso 4: Verificando configuración..."

# Verificar que el tracking esté habilitado
if [ -f "$PROJECT_DIR/application/config/database.php" ]; then
    print_info "Para activar el tracking, asegúrate de que en la base de datos:"
    echo "  site_config.SITEM_TRACK_VISITORS = 'Si'"
    echo ""
fi

print_success "Configuración verificada"
echo ""

# ========================================
# Paso 5: Verificar permisos
# ========================================
echo "🔒 Paso 5: Verificando permisos de archivos..."

# Dar permisos de ejecución si es necesario
chmod 644 "$PROJECT_DIR/public/js/analytics-client.min.js" 2>/dev/null
chmod 644 "$PROJECT_DIR/public/js/components/AnalyticsDashboard.js" 2>/dev/null

print_success "Permisos configurados"
echo ""

# ========================================
# Resumen Final
# ========================================
echo "╔════════════════════════════════════════════════════════╗"
echo "║              ✓ INSTALACIÓN COMPLETADA                 ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

print_success "El sistema de analytics está instalado y listo"
echo ""

echo "📋 Próximos pasos:"
echo ""
echo "1. Accede al dashboard:"
echo "   ${GREEN}http://tu-sitio.com/admin/analytics${NC}"
echo ""
echo "2. Verifica que el tracking funciona:"
echo "   - Visita tu sitio público"
echo "   - Revisa la tabla user_tracking en la BD"
echo ""
echo "3. Documentación:"
echo "   - INTEGRACION_AUTOMATICA.md (cómo usar)"
echo "   - docs/USER_TRACKING_IMPROVEMENTS.md (guía completa)"
echo "   - docs/ANALYTICS_USAGE_EXAMPLES.md (ejemplos)"
echo ""

print_info "Para soporte, revisa la documentación en docs/"
echo ""

echo "¡Gracias por usar Analytics System v2.0! 🚀"
