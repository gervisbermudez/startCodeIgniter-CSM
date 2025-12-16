# Build System

Este proyecto usa **Vite** para compilar SCSS únicamente. El JavaScript legacy se mantiene sin procesar para compatibilidad.

## Comandos

```bash
# Compilar SCSS para producción
npm run build

# Modo watch (recompila automáticamente los SCSS)
npm run watch

# Modo desarrollo (con servidor)
npm run dev
```

## Estructura

- **Input**: 
  - `resources/scss/admin/` - Estilos SCSS (compilados por Vite)
  - `resources/js/` - JavaScript (usado directamente, sin compilar)
  - `resources/components/` - Componentes Vue.js (usados directamente)

- **Output**: 
  - `public/css/admin/` - CSS compilado y minificado

## JavaScript Legacy

Los archivos JavaScript **NO** se procesan con Vite porque:
- Código legacy con variables globales (`Vue`, `$`, `M`, `mixins`)
- Vue 2 sin build system
- Compatible con carga directa en `<script>` tags

Si modificas archivos `.js`, se cargan directamente desde `resources/`.

## Migración desde Gulp

Migrado de Gulp a Vite (diciembre 2025) para compilación de SCSS:
- ✅ ~10x más rápido
- ✅ Menos dependencias
- ✅ JavaScript sin cambios (mantiene compatibilidad)

El archivo `gulpfile.js.backup` se mantiene como referencia.

