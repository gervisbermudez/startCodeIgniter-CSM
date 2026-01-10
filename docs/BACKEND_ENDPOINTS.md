# Endpoints del Backend - Admin Navbar

## ✅ Endpoints Implementados

Se han creado todos los endpoints necesarios para que las funciones del admin navbar contextual funcionen correctamente.

---

## 📦 1. Cache Management (`/admin/Cache.php`)

Nuevo controlador para gestionar el caché del sistema.

### **POST** `/admin/cache/clear-page/{page_id}`
Limpia el caché de una página específica.

**Permisos requeridos**: `UPDATE_PAGE`

**Respuesta exitosa**:
```json
{
  "success": true,
  "message": "Caché de la página limpiado correctamente",
  "page_id": 123
}
```

**Caché limpiado**:
- `page_{id}`
- `page_full_{id}`
- `page_content_{id}`
- `page_data_{id}`

---

### **POST** `/admin/cache/clear-blog/{blog_id}`
Limpia el caché de un post de blog.

**Permisos requeridos**: `UPDATE_BLOG`

**Respuesta exitosa**:
```json
{
  "success": true,
  "message": "Caché del blog limpiado correctamente",
  "blog_id": 456
}
```

**Caché limpiado**:
- `blog_{id}`
- `blog_full_{id}`
- `blog_content_{id}`
- `blog_list`
- `blog_recent`

---

### **POST** `/admin/cache/clear-all`
Limpia TODO el caché del sistema.

**Permisos requeridos**: `UPDATE_CONFIG`

**Respuesta exitosa**:
```json
{
  "success": true,
  "message": "Todo el caché ha sido limpiado",
  "files_cleaned": 42
}
```

**Nota**: No elimina archivos `.bladec` (caché de plantillas Blade).

---

### **GET** `/admin/cache/info`
Obtiene información sobre el estado del caché.

**Respuesta**:
```json
{
  "success": true,
  "data": {
    "cache_dir": "/path/to/cache/",
    "total_files": 150,
    "total_size": 2048576,
    "total_size_formatted": "2.00 MB",
    "blade_cache_files": 108
  }
}
```

---

## ⚙️ 2. Configuration (`/admin/ConfigurationController.php`)

Se agregó un nuevo método al controlador existente.

### **POST** `/admin/config/toggle-debug`
Activa o desactiva el modo debug del sistema.

**Permisos requeridos**: `UPDATE_CONFIG`

**Respuesta exitosa**:
```json
{
  "success": true,
  "debug_enabled": true,
  "message": "Debug activado"
}
```

**Funcionamiento**:
- Lee el valor actual de `DEBUG_MODE` en `site_config`
- Alterna entre `1` (activado) y `0` (desactivado)
- Si no existe la configuración, la crea con valor `1`
- Registra la acción en `system_logger`

---

## 📋 3. Site Forms (`/admin/SiteFormsController.php`)

Se agregaron dos nuevos métodos al controlador existente.

### **GET** `/admin/siteforms/export/{form_name}`
Exporta los envíos de un formulario a archivo CSV.

**Permisos requeridos**: `SELECT_SITEFORMS`

**Parámetros**:
- `form_name`: Nombre del formulario (URL encoded)

**Respuesta**:
- Descarga directa de archivo CSV
- Nombre: `form_{nombre}_{fecha}.csv`
- Encoding: UTF-8 con BOM (compatible con Excel)
- Separador: `;` (punto y coma para Excel en español)

**Estructura del CSV**:
```csv
ID;Fecha de Envío;IP;campo1;campo2;campo3
1;2026-01-10 14:30:00;192.168.1.1;Juan;juan@example.com;Mensaje
2;2026-01-10 15:45:00;192.168.1.2;María;maria@example.com;Consulta
```

**Características**:
- Detecta automáticamente todos los campos enviados
- Incluye ID, fecha, IP y todos los campos del formulario
- Maneja campos variables (diferentes campos en cada envío)

---

### **GET** `/admin/siteforms/stats/{form_name}`
Obtiene estadísticas de un formulario.

**Permisos requeridos**: `SELECT_SITEFORMS`

**Respuesta exitosa**:
```json
{
  "success": true,
  "data": {
    "form_name": "contacto",
    "total_submissions": 245,
    "unique_ips": 182,
    "first_submission": "2025-11-15 08:30:00",
    "last_submission": "2026-01-10 16:20:00",
    "daily_submissions": [
      {"date": "2025-12-11", "count": 5},
      {"date": "2025-12-12", "count": 8},
      {"date": "2025-12-13", "count": 3}
    ]
  }
}
```

**Estadísticas incluidas**:
- Total de envíos
- IPs únicas (para detectar spam)
- Primer y último envío
- Envíos por día (últimos 30 días)

---

## 🛣️ Rutas Configuradas

Se agregaron las siguientes rutas en `/application/config/routes.php`:

```php
// Cache routes
$route['admin/cache/clear-page/(:num)'] = 'admin/Cache/clear_page/$1';
$route['admin/cache/clear-blog/(:num)'] = 'admin/Cache/clear_blog/$1';
$route['admin/cache/clear-all'] = 'admin/Cache/clear_all';
$route['admin/cache/info'] = 'admin/Cache/info';
$route['admin/cache/(.+)'] = 'admin/Cache/$1';

// Configuration routes
$route['admin/config/toggle-debug'] = 'admin/ConfigurationController/toggle_debug';
$route['admin/config/(.+)'] = 'admin/ConfigurationController/$1';
$route['admin/config'] = 'admin/ConfigurationController';

// Siteforms routes
$route['admin/siteforms/export/(.+)'] = 'admin/SiteFormsController/export/$1';
$route['admin/siteforms/stats/(.+)'] = 'admin/SiteFormsController/stats/$1';
$route['admin/siteforms/(.+)'] = 'admin/SiteFormsController/$1';
$route['admin/siteforms'] = 'admin/SiteFormsController';
```

---

## 🔐 Seguridad

Todos los endpoints implementan:

### Autenticación
```php
if (!userdata('logged_in')) {
    // Retorna 401 Unauthorized
}
```

### Autorización por Permisos
- **Cache**: `UPDATE_PAGE`, `UPDATE_BLOG`, `UPDATE_CONFIG`
- **Debug**: `UPDATE_CONFIG`
- **Forms Export**: `SELECT_SITEFORMS`

### Logging de Acciones
Todas las acciones se registran en `system_logger`:
```php
system_logger('cache', $page_id, 'clear_page_cache', 'Mensaje descriptivo');
```

### Validación de Entrada
- Verificación de parámetros requeridos
- Validación de existencia de recursos
- Manejo de excepciones con try-catch

---

## 📊 Logging

Todas las acciones quedan registradas en la tabla `system_logger`:

| Acción | Tipo | Token |
|--------|------|-------|
| Limpiar caché de página | `cache` | `clear_page_cache` |
| Limpiar todo el caché | `cache` | `clear_all_cache` |
| Limpiar caché de blog | `cache` | `clear_blog_cache` |
| Toggle debug | `site_config` | `toggle_debug` |
| Exportar formulario | `siteforms` | `export_submissions` |

---

## 🧪 Pruebas Recomendadas

### 1. Probar Cache Clear
```bash
# Limpiar caché de página
curl -X POST http://localhost/admin/cache/clear-page/1 \
  -H "Cookie: ci_session=xxx"

# Limpiar todo el caché
curl -X POST http://localhost/admin/cache/clear-all \
  -H "Cookie: ci_session=xxx"
```

### 2. Probar Toggle Debug
```bash
curl -X POST http://localhost/admin/config/toggle-debug \
  -H "Cookie: ci_session=xxx"
```

### 3. Probar Export Forms
```bash
# Exportar formulario
curl http://localhost/admin/siteforms/export/contacto \
  -H "Cookie: ci_session=xxx" \
  -o contacto.csv

# Ver estadísticas
curl http://localhost/admin/siteforms/stats/contacto \
  -H "Cookie: ci_session=xxx"
```

---

## ✨ Integración con Admin Navbar

El admin navbar ya está configurado para usar estos endpoints:

### JavaScript
```javascript
// Limpiar caché de página
scmsAdminBar.clearPageCache(123);

// Limpiar todo el caché
scmsAdminBar.clearAllCache();

// Toggle debug
scmsAdminBar.toggleDebug();

// Exportar formulario
scmsAdminBar.exportFormData('contacto');
```

### Respuesta Visual
Los métodos usan **Materialize Toast** para mostrar feedback:
- ✅ Verde: Acción exitosa
- ❌ Rojo: Error
- ℹ️ Azul: Información

---

## 📝 Notas Adicionales

1. **Caché de Blade**: Los archivos `.bladec` NO se eliminan en `clear-all` para evitar regeneración innecesaria.

2. **CSV Encoding**: Se usa UTF-8 con BOM para compatibilidad con Excel en Windows.

3. **Separador CSV**: Punto y coma (`;`) para Excel en configuración regional española.

4. **Timeout**: Las operaciones de caché pueden tardar en sistemas con muchos archivos.

5. **Permisos de Archivo**: El usuario del servidor web debe tener permisos de escritura en `/application/cache/`.

---

**Fecha de implementación**: 10 de enero de 2026  
**Versión**: 1.0
