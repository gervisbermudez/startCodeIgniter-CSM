#!/bin/bash

# Script de validación de entorno para StartCodeIgniter-CSM
# Verifica que todas las dependencias y configuraciones estén correctas

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================="
echo "  Validación de Entorno - StartCMS"
echo "========================================="
echo ""

ERRORS=0
WARNINGS=0

# Función para verificar comandos
check_command() {
    if command -v $1 &> /dev/null; then
        VERSION=$($2)
        echo -e "${GREEN}✓${NC} $1 instalado: $VERSION"
        return 0
    else
        echo -e "${RED}✗${NC} $1 no encontrado"
        ((ERRORS++))
        return 1
    fi
}

# Función para verificar archivos
check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} $1 existe"
        return 0
    else
        echo -e "${RED}✗${NC} $1 no encontrado"
        ((ERRORS++))
        return 1
    fi
}

# Función para verificar directorios
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $1/ existe"
        return 0
    else
        echo -e "${YELLOW}⚠${NC} $1/ no encontrado"
        ((WARNINGS++))
        return 1
    fi
}

# Función para verificar permisos
check_permissions() {
    if [ -w "$1" ]; then
        echo -e "${GREEN}✓${NC} $1 tiene permisos de escritura"
        return 0
    else
        echo -e "${RED}✗${NC} $1 no tiene permisos de escritura"
        ((ERRORS++))
        return 1
    fi
}

echo "1. Verificando dependencias del sistema..."
echo "-------------------------------------------"
check_command "php" "php -v | head -n1"
check_command "composer" "composer --version | head -n1"
check_command "node" "node --version"
check_command "npm" "npm --version"

# Verificar versión de PHP
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(echo $PHP_VERSION | cut -d. -f1)
PHP_MINOR=$(echo $PHP_VERSION | cut -d. -f2)

if [ "$PHP_MAJOR" -ge 7 ] && [ "$PHP_MINOR" -ge 4 ]; then
    echo -e "${GREEN}✓${NC} Versión de PHP compatible (>= 7.4)"
else
    echo -e "${YELLOW}⚠${NC} PHP 7.4+ recomendado (actual: $PHP_VERSION)"
    ((WARNINGS++))
fi

echo ""
echo "2. Verificando archivos de configuración..."
echo "-------------------------------------------"
check_file ".env.example"
check_file "composer.json"
check_file "package.json"
check_file "vite.config.js"
check_file ".editorconfig"
check_file ".gitignore"
check_file ".gitattributes"

if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠${NC} .env no encontrado (copiar desde .env.example)"
    ((WARNINGS++))
else
    echo -e "${GREEN}✓${NC} .env existe"
fi

echo ""
echo "3. Verificando estructura de directorios..."
echo "-------------------------------------------"
check_dir "application"
check_dir "public"
check_dir "resources"
check_dir "vendor"
check_dir "node_modules"
check_dir "application/cache"
check_dir "application/logs"
check_dir "uploads"

echo ""
echo "4. Verificando permisos de escritura..."
echo "-------------------------------------------"
check_permissions "application/cache"
check_permissions "application/logs"
check_permissions "uploads"

echo ""
echo "5. Verificando assets compilados..."
echo "-------------------------------------------"
check_dir "public/css/admin"
check_dir "public/fonts"
check_dir "public/vendors"

if [ -f "public/css/admin/start.min.css" ]; then
    echo -e "${GREEN}✓${NC} CSS compilado"
else
    echo -e "${YELLOW}⚠${NC} CSS no compilado (ejecutar: npm run build)"
    ((WARNINGS++))
fi

echo ""
echo "6. Verificando dependencias de PHP..."
echo "-------------------------------------------"
if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✓${NC} Dependencias de Composer instaladas"
else
    echo -e "${RED}✗${NC} Dependencias de Composer no instaladas (ejecutar: composer install)"
    ((ERRORS++))
fi

echo ""
echo "7. Verificando dependencias de Node..."
echo "-------------------------------------------"
if [ -d "node_modules" ]; then
    echo -e "${GREEN}✓${NC} Dependencias de NPM instaladas"
else
    echo -e "${RED}✗${NC} Dependencias de NPM no instaladas (ejecutar: npm install)"
    ((ERRORS++))
fi

echo ""
echo "========================================="
echo "  Resumen"
echo "========================================="
echo -e "Errores:      ${RED}$ERRORS${NC}"
echo -e "Advertencias: ${YELLOW}$WARNINGS${NC}"
echo ""

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✓ Entorno listo para desarrollo${NC}"
    exit 0
else
    echo -e "${RED}✗ Por favor, corrige los errores antes de continuar${NC}"
    exit 1
fi
