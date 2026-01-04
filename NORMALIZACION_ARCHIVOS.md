# Normalización de Archivos en Resources

## Fecha: 4 de enero de 2026

### Resumen de Cambios

Se ha realizado una normalización de los nombres de archivos en la carpeta `/resources` para mantener consistencia en el idioma (inglés) y las convenciones de nomenclatura.

### Cambios Realizados

#### 1. Normalización de Archivos con Nombres en Español

| Archivo Anterior | Archivo Nuevo | Ubicación | Tipo |
|---|---|---|---|
| `ConfiguracionList.js` | `ConfigurationList.js` | `resources/components/` | Componente Vue |
| `albumesWidgetComponent.js` | `AlbumsWidgetComponent.js` | `resources/components/widget/` | Widget Vue |

#### 2. Archivos Actualizados en la Carpeta Public

| Archivo Anterior | Archivo Nuevo | Ubicación |
|---|---|---|
| `ConfiguracionList.js` | `ConfigurationList.js` | `public/js/components/` |

### Referencias Actualizadas

#### En Vistas Blade.php:

**Archivo:** `/application/views/admin/configuration/all_config.blade.php`
- Línea 348: Actualizado `ConfiguracionList.js` → `ConfigurationList.js`

**Archivo:** `/application/views/admin/dashboard.blade.php`
- Línea 293: Actualizado `@include('admin.components.albumesWidgetComponent')` → `@include('admin.components.AlbumsWidgetComponent')`
- Línea 299: Actualizado ruta de script `albumesWidgetComponent.js` → `AlbumsWidgetComponent.js`

### Esquema de Normalización Aplicado

- ✅ Todo en **inglés** (sin español)
- ✅ Componentes Vue: `PascalCaseComponent.js`
- ✅ Módulos: `camelCaseModule.js`
- ✅ Nombres claros y consistentes sin abreviaturas
- ✅ Convenciones de nomenclatura uniformes

### Beneficios

1. **Consistencia**: Todos los archivos siguen la misma convención
2. **Claridad**: Nombres en inglés facilitan búsquedas internacionales
3. **Mantenibilidad**: Estructura más clara y organizada
4. **Escalabilidad**: Facilita la adición de nuevos componentes

### Notas

- Los cambios se propagaron a través de todas las referencias encontradas
- Se mantiene la funcionalidad del código sin alteraciones
- Recomendación: Ejecutar `npm run build` para regenerar archivos compilados

