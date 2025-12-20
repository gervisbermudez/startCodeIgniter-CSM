# ✅ INTEGRACIÓN AUTOMÁTICA - Analytics System

## 🎯 Estado: INTEGRADO Y LISTO PARA USAR

El sistema de analytics **YA ESTÁ INTEGRADO** automáticamente en tu CMS. No necesitas configuración adicional.

---

## 🚀 ¿Qué se Hizo?

### ✅ Backend (PHP) - Tracking Automático
1. **`Base_Controller`** actualizado para usar `Track_Visitor_Enhanced`
2. **Helper `analytics`** agregado al autoload
3. **Tracking automático** en TODAS las páginas públicas si `SITEM_TRACK_VISITORS = 'Si'`

### ✅ Frontend (JavaScript) - Tracking Avanzado
1. **Script de analytics** incluido automáticamente en el layout del tema
2. **Tracking automático** de:
   - Clicks en botones y enlaces
   - Scroll depth (25%, 50%, 75%, 100%)
   - Tiempo en página
   - Formularios
   - Descargas de archivos
   - Enlaces externos
   - Errores JavaScript

### ✅ Dashboard de Analytics
1. **Ruta configurada**: `/admin/analytics`
2. **Componente Vue** incluido automáticamente
3. **15 endpoints API** disponibles

---

## 📋 SOLO NECESITAS HACER ESTO:

### Paso 1: Ejecutar Migración de Base de Datos ⚠️

```bash
cd /home/gervis/Documentos/startCodeIgniter-CSM

# Conectar a MySQL
mysql -u root -p nombre_de_tu_base_datos

# Dentro de MySQL, ejecutar:
source application/database/migrations/001_improve_user_tracking.sql;
```

**¡Eso es TODO!** 🎉

---

## 🔍 Verificar que Funciona

### 1. Ver el Tracking en Acción

1. Visita cualquier página de tu sitio público
2. El tracking se ejecutará automáticamente (si `SITEM_TRACK_VISITORS = 'Si'`)
3. Revisa la base de datos:

```sql
SELECT * FROM user_tracking ORDER BY date_create DESC LIMIT 10;
SELECT * FROM user_sessions ORDER BY first_visit DESC LIMIT 10;
```

### 2. Acceder al Dashboard

Hay **3 formas** de acceder:

**Opción 1: Desde el menú lateral del admin**
- Inicia sesión en el admin
- En el menú lateral verás **"Analytics"** con icono 📊
- Click y listo!

**Opción 2: URL directa**
```
http://tu-sitio.com/admin/configuration/analytics
```

**Opción 3: Desde Configuración**
- Admin → Configuración → hay un enlace a Analytics

Verás:
- 📊 Gráficos de tendencias
- 📈 Métricas clave (sesiones, bounce rate, conversiones)
- 📱 Estadísticas por dispositivo
- ⏱️ Visitantes en tiempo real
- 📥 Exportación a CSV

---

## 🎨 ¿Qué Está Tracking AUTOMÁTICAMENTE?

### Sin Hacer Nada:
- ✅ **Pageviews** - Cada visita a una página
- ✅ **Sesiones** - Agrupadas por usuario
- ✅ **Dispositivos** - Desktop, Mobile, Tablet
- ✅ **Navegadores** - Chrome, Firefox, Safari, etc.
- ✅ **Tiempo en página** - Cuánto tiempo pasan los usuarios
- ✅ **Bounce rate** - Si solo ven 1 página y se van
- ✅ **Clicks** - En botones y enlaces
- ✅ **Scroll** - Qué tan abajo llegan en la página
- ✅ **Formularios** - Cuando se envían

### Con Código Simple (Opcional):

```javascript
// En cualquier página del sitio
trackEvent('Button', 'Click', 'Subscribe');
trackConversion(); // Cuando completan una compra o registro
```

---

## 📊 Ubicación de Archivos Integrados

### Backend Modificado:
- ✅ `application/core/MY_Controller.php` (líneas 248-251)
- ✅ `application/config/autoload.php` (línea 70)

### Frontend Modificado:
- ✅ `themes/awesomeTheme/views/site/layouts/site.blade.php` (líneas 13-16)

### Archivos Nuevos Activos:
- ✅ `application/libraries/Track_Visitor_Enhanced.php`
- ✅ `application/helpers/analytics_helper.php`
- ✅ `public/js/analytics-client.min.js`
- ✅ `public/js/components/AnalyticsDashboard.js`

---

## 🛠️ Configuración (Opcional)

### Activar/Desactivar Tracking

En la base de datos, tabla `site_config`:

```sql
-- Activar
UPDATE site_config SET config_value = 'Si' WHERE config_name = 'SITEM_TRACK_VISITORS';

-- Desactivar
UPDATE site_config SET config_value = 'No' WHERE config_name = 'SITEM_TRACK_VISITORS';
```

### Modificar Comportamiento del Tracking

Edita `public/js/analytics-client.min.js`, líneas 10-17:

```javascript
const CONFIG = {
  apiEndpoint: '/api/v1/analytics',
  autoTrack: true,           // ← Tracking automático
  trackClicks: true,         // ← Clicks
  trackScroll: true,         // ← Scroll
  trackFormSubmits: true,    // ← Formularios
  trackPageTime: true,       // ← Tiempo en página
  sendInterval: 5000,        // ← Enviar cada 5 segundos
};
```

---

## 📈 Usar Analytics en tus Vistas

### En PHP (Blade Templates):

```php
@php
  $stats = get_analytics_stats('overview', [
    'start_date' => date('Y-m-d', strtotime('-7 days')),
    'end_date' => date('Y-m-d')
  ]);
@endphp

<div class="stats">
  <h3>Visitantes esta semana: {{ $stats['unique_visitors'] }}</h3>
  <p>Tasa de conversión: {{ $stats['conversion_rate'] }}%</p>
</div>
```

### En JavaScript (Eventos Personalizados):

```javascript
// Track cuando un usuario ve un video
document.getElementById('video').addEventListener('play', function() {
  trackEvent('Video', 'Play', 'Tutorial Video');
});

// Track cuando completan una compra
function onPurchaseComplete(orderId, amount) {
  trackConversion();
  trackEvent('Purchase', 'Complete', orderId, amount);
}
```

---

## 🎯 Endpoints API Disponibles

Todos funcionan SIN autenticación adicional (usa las sesiones del CMS):

```
GET  /api/v1/analytics/overview
GET  /api/v1/analytics/trend
GET  /api/v1/analytics/popular-pages
GET  /api/v1/analytics/traffic-sources
GET  /api/v1/analytics/devices
GET  /api/v1/analytics/realtime
GET  /api/v1/analytics/export  (descarga CSV)
POST /api/v1/analytics/event   (custom events)
POST /api/v1/analytics/conversion
```

---

## 🔧 Migrar Datos Existentes (Opcional)

Si ya tienes datos en `user_tracking`, puedes migrarlos:

```bash
mysql -u root -p nombre_de_tu_base_datos < application/database/migrations/002_migrate_existing_data.sql
```

Esto:
- Genera `session_id` para registros antiguos
- Detecta tipo de dispositivo desde `user_agent`
- Crea sesiones agrupadas
- Marca bounces y exit pages

---

## ❓ Troubleshooting

### No veo datos en el dashboard

1. **Verifica que el tracking esté activo:**
```sql
SELECT * FROM site_config WHERE config_name = 'SITEM_TRACK_VISITORS';
```

2. **Verifica que la migración se ejecutó:**
```sql
SHOW COLUMNS FROM user_tracking LIKE 'session_id';
```

3. **Verifica que el JavaScript se carga:**
   - Abre el sitio público
   - Presiona F12 (consola del navegador)
   - Ve a "Network" → busca `analytics-client.min.js`

### Error en el dashboard

**Ejecuta la migración** (Paso 1 arriba). El dashboard necesita las nuevas tablas.

### Las conversiones no se registran

```javascript
// Asegúrate de llamar esto cuando el usuario complete una acción:
trackConversion();
```

---

## 📚 Documentación Completa

Ver archivos detallados en:
- `docs/USER_TRACKING_IMPROVEMENTS.md` - Guía completa
- `docs/ANALYTICS_USAGE_EXAMPLES.md` - Ejemplos de código

---

## 🎉 ¡Ya Está Funcionando!

Solo ejecuta la migración SQL y **todo funcionará automáticamente**. El tracking se activará en:

✅ Todas las páginas públicas
✅ Todas las páginas del blog  
✅ Formularios de contacto
✅ Páginas de portafolio
✅ Cualquier página que use `Base_Controller`

**Dashboard disponible en**: `http://tu-sitio.com/admin/analytics`

---

**Creado**: Diciembre 2025  
**Versión**: 2.0 Integrado
