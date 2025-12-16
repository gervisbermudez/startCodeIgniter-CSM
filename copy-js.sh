#!/bin/bash
# Script para copiar y minificar JavaScript sin cambiar su naturaleza

echo "=== Copiando archivos JS sin modificar ==="

# Copiar archivos desde resources directamente a public
cp -f resources/js/*.js public/js/
cp -f resources/components/*.js public/js/components/

echo "✅ Archivos JavaScript copiados sin modificar"
echo "   (mantienen compatibilidad con código legacy)"
