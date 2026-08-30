#!/bin/bash
# Script para copiar JavaScript sin cambiar su naturaleza

ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
cd "$ROOT_DIR"

echo "=== Copiando archivos JS sin modificar ==="

# Copiar archivos desde resources directamente a public
cp -f resources/js/*.js public/js/
cp -f resources/components/*.js public/js/components/

echo "✅ Archivos JavaScript copiados sin modificar"
echo "   (mantienen compatibilidad con código legacy)"
