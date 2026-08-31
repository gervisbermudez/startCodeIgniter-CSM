# Directorio de Backups de Base de Datos

Este directorio almacena las copias de seguridad automáticas de la base de datos.

## Estructura

- Los backups se generan en formato `.gz` (comprimido)
- Nomenclatura: `YYYYMMDDHHmmss.gz` (ejemplo: `20231221133000.gz`)

## Gestión

- **Crear backup**: Admin → Settings → Data / backups (`/admin/configuration/data`)
- **Descargar backup**: Click en el botón de descarga en la lista de backups
- **Eliminar backup**: Click en el botón de eliminar (requiere confirmación)

## Permisos

Este directorio requiere permisos de escritura (777) para que el servidor web pueda crear backups.

```bash
chmod -R 777 backups/
```

## Seguridad

⚠️ **IMPORTANTE**: Los archivos `.gz` están excluidos del control de versiones por seguridad.
Asegúrate de mantener copias de seguridad en un lugar seguro fuera del servidor.

## Limpieza

Se recomienda eliminar backups antiguos periódicamente para liberar espacio en disco.
