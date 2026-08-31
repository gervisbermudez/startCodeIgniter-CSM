# User Tracking — mejoras (base del módulo Analytics)

> **Estado (Start CMS 3.0):** shipped. El producto se ve en **Analytics** (`/admin/analytics`). Este documento describe el schema y la librería (`Track_Visitor_Enhanced`, migración `001` / `003`).

## 🎯 Resumen de Mejoras

Este documento detalla las mejoras implementadas en el sistema de tracking de usuarios, transformándolo en una solución completa de analytics.

## ✨ Características Nuevas

### 1. Base de Datos Mejorada
- ✅ **Tabla `user_tracking` ampliada** con campos adicionales:
  - `session_id`: Identificador de sesión único
  - `browser`, `browser_version`, `platform`: Información del navegador
  - `device_type`: Tipo de dispositivo (desktop, mobile, tablet, bot)
  - `screen_resolution`, `language`: Datos del cliente
  - `country_code`, `city`: Geolocalización
  - `time_on_page`: Tiempo en página (segundos)
  - `is_bounce`, `exit_page`, `conversion`: Métricas de engagement
   
- ✅ **Nueva tabla `user_sessions`**: Tracking de sesiones completas
- ✅ **Nueva tabla `user_tracking_events`**: Eventos personalizados
- ✅ **Nueva tabla `user_tracking_daily_stats`**: Estadísticas pre-calculadas
- ✅ **Vistas SQL** para consultas rápidas
- ✅ **Stored Procedures** para cálculos automáticos
- ✅ **Índices optimizados** para mejor rendimiento

### 2. Librería de Tracking Mejorada
**Archivo**: `application/libraries/Track_Visitor_Enhanced.php`

- ✅ **Gestión de sesiones** con cookies persistentes
- ✅ **Detección avanzada de dispositivos** (móvil, tablet, desktop)
- ✅ **Tracking de tiempo en página** automático
- ✅ **Detección de bounce rate** en tiempo real
- ✅ **Soporte para UTM parameters** (campañas de marketing)
- ✅ **API para eventos personalizados**
- ✅ **Tracking de conversiones**

### 3. Modelo de Analytics Completo
**Archivo**: `application/models/Admin/UserTrackingModelEnhanced.php`

Métodos nuevos:
- `get_overview_stats()` - Estadísticas generales
- `get_daily_trend()` - Tendencia diaria
- `get_popular_pages()` - Páginas más visitadas
- `get_traffic_sources()` - Fuentes de tráfico
- `get_device_stats()` - Estadísticas por dispositivo
- `get_browser_stats()` - Estadísticas por navegador
- `get_geographic_stats()` - Distribución geográfica
- `get_realtime_visitors()` - Visitantes en tiempo real
- `get_conversion_funnel()` - Embudo de conversión
- `get_hourly_distribution()` - Distribución por hora
- `search_with_filters()` - Búsqueda avanzada
- `export_to_csv()` - Exportación de datos

### 4. API Endpoints Completos
**Archivo**: `application/controllers/api/v1/AnalyticsController.php`

Endpoints disponibles:
```
GET  /api/v1/analytics/overview          - Resumen general
GET  /api/v1/analytics/trend              - Tendencia diaria
GET  /api/v1/analytics/popular-pages      - Páginas populares
GET  /api/v1/analytics/traffic-sources    - Fuentes de tráfico
GET  /api/v1/analytics/devices            - Estadísticas de dispositivos
GET  /api/v1/analytics/browsers           - Estadísticas de navegadores
GET  /api/v1/analytics/geographic         - Distribución geográfica
GET  /api/v1/analytics/realtime           - Visitantes en tiempo real
GET  /api/v1/analytics/hourly             - Distribución por hora
POST /api/v1/analytics/funnel             - Embudo de conversión
GET  /api/v1/analytics/export             - Exportar a CSV
GET  /api/v1/analytics/search             - Búsqueda con filtros
GET  /api/v1/analytics/dashboard          - Todos los datos en una llamada
POST /api/v1/analytics/event              - Registrar evento personalizado
POST /api/v1/analytics/conversion         - Registrar conversión
```

### 5. Dashboard de Analytics
**Archivos**:
- Vista: `application/views/admin/analytics/dashboard.blade.php`
- Componente Vue: `resources/components/AnalyticsDashboard.js`

Características:
- 📊 **Gráficos interactivos** (Chart.js):
  - Tendencia de tráfico (línea)
  - Distribución de dispositivos (dona)
  - Páginas más visitadas (barras horizontales)
  
- 📈 **Métricas clave**:
  - Total de sesiones
  - Visitantes únicos
  - Páginas vistas
  - Tiempo promedio en página
  - Tasa de rebote
  - Tasa de conversión
  - Páginas por sesión

- 🔍 **Filtros avanzados**:
  - Rango de fechas
  - Tipo de dispositivo
  - País/Ciudad
  - Estado de conversión

- ⏱️ **Visitantes en tiempo real** (actualización automática cada 30s)
- 📥 **Exportación a CSV**
- 📱 **Responsive design**

## 🚀 Instalación

### Paso 1: Migración de Base de Datos

```bash
# Conectar a MySQL
mysql -u root -p nombre_base_datos

# Ejecutar migración
source application/database/migrations/001_improve_user_tracking.sql
```

### Paso 2: Configurar la Librería

Editar `application/config/autoload.php`:
```php
$autoload['libraries'] = array('Track_Visitor_Enhanced');
```

O cargarla manualmente en tu controlador:
```php
$this->load->library('Track_Visitor_Enhanced', null, 'tracker');
```

### Paso 3: Actualizar el Hook o Autoload

En `application/hooks/` o donde inicialices el tracking:

```php
// Opción 1: Reemplazar la librería antigua
$this->load->library('Track_Visitor_Enhanced', null, 'tracker');
$this->tracker->visitor_track();

// Opción 2: Usar ambas (para transición)
// Mantén Track_Visitor y añade Track_Visitor_Enhanced
```

### Paso 4: Compilar Assets

```bash
# Si usas npm/vite
npm run build

# O copiar manualmente
cp resources/components/AnalyticsDashboard.js public/js/components/
```

### Paso 5: Configurar Rutas

Agregar en `application/config/routes.php` o en tu sistema de rutas:

```php
$route['admin/analytics'] = 'admin/ConfigurationController/analytics';
```

### Paso 6: Acceder al Dashboard

```
http://tu-sitio.com/admin/analytics
```

## 📝 Uso

### Tracking Básico
El tracking automático ya funciona con la librería mejorada. No requiere cambios en tu código existente.

### Eventos Personalizados

```javascript
// Desde el frontend
fetch('/api/v1/analytics/event', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    category: 'Button',
    action: 'Click',
    label: 'Download PDF',
    value: 1
  })
});
```

```php
// Desde el backend
$this->load->library('Track_Visitor_Enhanced', null, 'tracker');
$this->tracker->track_event('Form', 'Submit', 'Contact Form', 1);
```

### Tracking de Conversiones

```php
// Cuando un usuario completa una acción importante
$this->load->library('Track_Visitor_Enhanced', null, 'tracker');
$this->tracker->track_conversion();
```

### Obtener Estadísticas Programáticamente

```php
$this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');

// Resumen de los últimos 30 días
$stats = $this->analytics->get_overview_stats(
    date('Y-m-d', strtotime('-30 days')),
    date('Y-m-d')
);

// Páginas más populares
$popular = $this->analytics->get_popular_pages(10);

// Visitantes en tiempo real
$realtime = $this->analytics->get_realtime_visitors();
```

## 🔧 Mantenimiento

### Limpieza de Datos Antiguos (Cron Job)

Agregar a crontab:
```bash
# Limpiar sesiones de más de 30 días (ejecutar diariamente)
0 2 * * * php /path/to/your/project/index.php cli/maintenance clean_sessions
```

Crear controlador CLI:
```php
// application/controllers/cli/Maintenance.php
class Maintenance extends CI_Controller {
    public function clean_sessions() {
        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        $deleted = $this->tracker->clean_old_sessions(30);
        echo "Deleted $deleted old sessions\n";
    }
    
    public function calculate_stats() {
        $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
        $this->analytics->calculate_daily_stats();
        echo "Daily stats calculated\n";
    }
}
```

## 📊 Métricas Explicadas

- **Sesiones**: Visitas únicas (expiran después de 30 minutos de inactividad)
- **Visitantes únicos**: Basado en IP + User Agent
- **Bounce Rate**: % de sesiones con solo 1 página vista
- **Conversion Rate**: % de sesiones que completaron una conversión
- **Time on Page**: Tiempo promedio que los usuarios pasan en una página
- **Pages per Session**: Promedio de páginas vistas por sesión

## 🎨 Personalización

### Cambiar Colores del Dashboard

Editar `application/views/admin/analytics/dashboard.blade.php`:
```css
.metric-card.blue {
    background: linear-gradient(135deg, #TU_COLOR 0%, #TU_COLOR_OSCURO 100%);
}
```

### Agregar Nuevas Métricas

1. Agregar método en `UserTrackingModelEnhanced.php`
2. Crear endpoint en `AnalyticsController.php`
3. Agregar al componente Vue `AnalyticsDashboard.js`
4. Actualizar la vista HTML

## 🐛 Troubleshooting

**Problema**: Los gráficos no se muestran
- Verificar que Chart.js esté cargado
- Revisar consola del navegador para errores
- Verificar que los canvas tengan IDs correctos

**Problema**: No se registran visitas
- Verificar que la librería esté cargada
- Revisar que el usuario no esté en la lista de IPs ignoradas
- Verificar que el controlador no esté en la lista de ignorados

**Problema**: Sesiones duplicadas
- Limpiar cookies del navegador
- Verificar tiempo de expiración de sesiones

## 📈 Próximas Mejoras Sugeridas

- [ ] Integración con API de Geolocalización (MaxMind, IP-API)
- [ ] Heatmaps de clicks
- [ ] Grabación de sesiones
- [ ] A/B Testing integrado
- [ ] Alertas automáticas
- [ ] Reportes por email
- [ ] Comparación de períodos
- [ ] Segmentación de usuarios
- [ ] Integración con Google Analytics
- [ ] Panel de administración de eventos

## 📞 Soporte

Para reportar problemas o sugerir mejoras, crear un issue en el repositorio.

---

**Versión**: 2.0  
**Fecha**: Diciembre 2025  
**Compatibilidad**: CodeIgniter 3.x
