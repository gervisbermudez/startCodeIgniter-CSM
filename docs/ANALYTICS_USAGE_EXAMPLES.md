# Ejemplos de uso — Analytics

> **Estado (Start CMS 3.0):** shipped como módulo admin (`/admin/analytics`) más el tracker de sitio. Este archivo son ejemplos de API/helpers; el dashboard vive en `AnalyticsAdminController` y `AnalyticsDashboard.js`.

## 📚 Tabla de Contenidos
1. [Tracking Básico](#tracking-básico)
2. [Eventos Personalizados](#eventos-personalizados)
3. [Conversiones](#conversiones)
4. [Consultas de Analytics](#consultas-de-analytics)
5. [Dashboard Personalizado](#dashboard-personalizado)
6. [Integración Frontend](#integración-frontend)

---

## Tracking Básico

### Activar Tracking Automático

El tracking automático se activa simplemente cargando la librería:

```php
// En application/config/autoload.php
$autoload['libraries'] = array('Track_Visitor_Enhanced');

// O en un hook (application/hooks/)
$hook['post_controller_constructor'][] = array(
    'class'    => 'Track_Visitor_Enhanced',
    'function' => 'visitor_track',
    'filename' => 'Track_Visitor_Enhanced.php',
    'filepath' => 'libraries'
);
```

---

## Eventos Personalizados

### Desde PHP (Backend)

```php
// En un controlador
class ContactController extends CI_Controller {
    
    public function submit() {
        // Procesar formulario...
        
        // Track evento de envío
        $this->load->helper('analytics');
        track_event('Form', 'Submit', 'Contact Form', 1);
        
        // O usando la librería directamente
        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        $this->tracker->track_event('Form', 'Submit', 'Contact Form', 1, [
            'email' => $this->input->post('email'),
            'subject' => $this->input->post('subject')
        ]);
    }
}
```

### Desde JavaScript (Frontend)

```javascript
// Incluir el script
<script src="/resources/js/analytics-client.js"></script>

// Track click en botón
document.getElementById('subscribe-btn').addEventListener('click', function() {
    trackEvent('Button', 'Click', 'Subscribe Newsletter');
});

// Track video play
document.getElementById('video').addEventListener('play', function() {
    trackEvent('Video', 'Play', 'Intro Video', null, {
        duration: this.duration,
        currentTime: this.currentTime
    });
});

// Track scroll milestone
trackEvent('Scroll', 'Depth', '75%', 75);
```

---

## Conversiones

### Tracking de Conversión Simple

```php
// Cuando un usuario completa una compra
class CheckoutController extends CI_Controller {
    
    public function complete() {
        // Procesar compra...
        
        // Track conversión
        $this->load->helper('analytics');
        track_conversion();
        
        // También puedes trackear como evento
        track_event('Conversion', 'Purchase', 'Product XYZ', $total_amount, [
            'order_id' => $order_id,
            'product_count' => $item_count
        ]);
    }
}
```

### Múltiples Tipos de Conversión

```php
// Diferentes conversiones
track_event('Conversion', 'Newsletter Signup', 'Footer Form');
track_event('Conversion', 'Account Created', 'Registration');
track_event('Conversion', 'Download', 'Ebook PDF');
track_event('Conversion', 'Contact', 'Contact Form');
```

---

## Consultas de Analytics

### Obtener Estadísticas en un Controlador

```php
class DashboardController extends CI_Controller {
    
    public function index() {
        $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
        
        // Estadísticas de los últimos 30 días
        $stats = $this->analytics->get_overview_stats(
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        );
        
        $data = [
            'total_visitors' => $stats['unique_visitors'],
            'total_pageviews' => $stats['total_pageviews'],
            'bounce_rate' => $stats['bounce_rate'],
            'conversion_rate' => $stats['conversion_rate']
        ];
        
        $this->load->view('dashboard', $data);
    }
}
```

### Páginas Más Visitadas

```php
$this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');

// Top 10 páginas del último mes
$popular_pages = $this->analytics->get_popular_pages(
    10, // límite
    date('Y-m-d', strtotime('-30 days')), // fecha inicio
    date('Y-m-d') // fecha fin
);

foreach ($popular_pages as $page) {
    echo "{$page['page_name']}: {$page['visits']} visitas<br>";
    echo "Bounce rate: {$page['bounce_rate']}%<br>";
    echo "Tiempo promedio: {$page['avg_time']}s<br><br>";
}
```

### Estadísticas por Dispositivo

```php
$device_stats = $this->analytics->get_device_stats(
    date('Y-m-d', strtotime('-7 days')),
    date('Y-m-d')
);

foreach ($device_stats as $device) {
    echo "{$device['device_type']}: ";
    echo "{$device['percentage']}% ";
    echo "({$device['sessions']} sesiones)<br>";
}
```

### Visitantes en Tiempo Real

```php
$realtime = $this->analytics->get_realtime_visitors();

echo "Visitantes activos (últimos 30 min): " . count($realtime) . "<br>";

foreach ($realtime as $visitor) {
    echo "Página: {$visitor['page_name']}<br>";
    echo "Sesiones activas: {$visitor['active_sessions']}<br>";
}
```

---

## Dashboard Personalizado

### Vista Personalizada con Métricas

```php
// En tu vista
$this->load->helper('analytics');

// Obtener estadísticas
$stats = get_analytics_stats('overview', [
    'start_date' => date('Y-m-d', strtotime('-7 days')),
    'end_date' => date('Y-m-d')
]);
?>

<div class="analytics-dashboard">
    <div class="metric-card">
        <h3>Visitantes Únicos</h3>
        <div class="value"><?= format_analytics_metric($stats['unique_visitors'], 'number') ?></div>
    </div>
    
    <div class="metric-card">
        <h3>Tasa de Rebote</h3>
        <div class="value <?= get_bounce_rate_color($stats['bounce_rate']) ?>">
            <?= format_analytics_metric($stats['bounce_rate'], 'percentage') ?>
        </div>
    </div>
    
    <div class="metric-card">
        <h3>Tasa de Conversión</h3>
        <div class="value <?= get_conversion_rate_color($stats['conversion_rate']) ?>">
            <?= format_analytics_metric($stats['conversion_rate'], 'percentage') ?>
        </div>
    </div>
    
    <div class="metric-card">
        <h3>Tiempo Promedio</h3>
        <div class="value"><?= format_analytics_metric($stats['avg_time_on_page'], 'time') ?></div>
    </div>
</div>
```

### Widget de Páginas Populares

```php
$popular = get_analytics_stats('pages', ['limit' => 5]);
?>

<div class="popular-pages-widget">
    <h3>Páginas Más Visitadas</h3>
    <table>
        <thead>
            <tr>
                <th>Página</th>
                <th>Visitas</th>
                <th>Bounce Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($popular as $page): ?>
            <tr>
                <td><?= $page['page_name'] ?></td>
                <td><?= format_analytics_metric($page['visits'], 'number') ?></td>
                <td><?= format_analytics_metric($page['bounce_rate'], 'percentage') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

---

## Integración Frontend

### Tracking Automático con HTML Data Attributes

```html
<!-- Track clicks automáticamente -->
<button data-track data-track-label="Download Brochure">
    Descargar Brochure
</button>

<a href="/pricing" data-track data-track-label="View Pricing">
    Ver Precios
</a>
```

### Tracking Manual de Eventos Específicos

```javascript
// Track reproducción de video
const video = document.getElementById('myVideo');

video.addEventListener('play', function() {
    trackEvent('Video', 'Play', this.src);
});

video.addEventListener('pause', function() {
    trackEvent('Video', 'Pause', this.src, this.currentTime);
});

video.addEventListener('ended', function() {
    trackEvent('Video', 'Completed', this.src);
});
```

### Tracking de Formularios con Validación

```javascript
const form = document.getElementById('contact-form');

form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar formulario
    if (validateForm()) {
        // Track evento exitoso
        trackEvent('Form', 'Submit', 'Contact Form', 1, {
            email: form.email.value,
            message_length: form.message.value.length
        });
        
        // Enviar formulario
        this.submit();
    } else {
        // Track error de validación
        trackEvent('Form', 'Validation Error', 'Contact Form');
    }
});
```

### Tracking de E-commerce

```javascript
// Añadir al carrito
function addToCart(productId, productName, price) {
    // Añadir producto...
    
    trackEvent('Ecommerce', 'Add to Cart', productName, price, {
        product_id: productId,
        category: 'Electronics'
    });
}

// Remover del carrito
function removeFromCart(productId, productName) {
    trackEvent('Ecommerce', 'Remove from Cart', productName, null, {
        product_id: productId
    });
}

// Checkout iniciado
function startCheckout(cartTotal, itemCount) {
    trackEvent('Ecommerce', 'Checkout Started', null, cartTotal, {
        item_count: itemCount
    });
}

// Compra completada
function purchaseComplete(orderId, total) {
    trackConversion();
    trackEvent('Ecommerce', 'Purchase', orderId, total, {
        order_id: orderId
    });
}
```

---

## Búsqueda Avanzada con Filtros

```php
$this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');

// Buscar visitas de móviles en diciembre con conversión
$results = $this->analytics->search_with_filters([
    'start_date' => '2025-12-01',
    'end_date' => '2025-12-31',
    'device_type' => 'mobile',
    'conversion' => 1,
    'limit' => 100
]);
```

---

## Exportación de Datos

### Exportar con Filtros

```php
class ReportsController extends CI_Controller {
    
    public function export_monthly() {
        $this->load->helper('analytics');
        
        export_analytics_csv([
            'start_date' => date('Y-m-01'), // Primer día del mes
            'end_date' => date('Y-m-d'),     // Hoy
            'limit' => 10000
        ], 'monthly_report_' . date('Y-m') . '.csv');
    }
}
```

### Exportar Dispositivos Móviles

```php
export_analytics_csv([
    'device_type' => 'mobile',
    'start_date' => date('Y-m-d', strtotime('-7 days')),
    'end_date' => date('Y-m-d')
], 'mobile_visitors.csv');
```

---

## Embudo de Conversión

```php
$funnel_pages = [
    'home/index',
    'products/catalog',
    'products/details',
    'cart/view',
    'checkout/process',
    'checkout/complete'
];

$funnel = $this->analytics->get_conversion_funnel($funnel_pages);

foreach ($funnel as $step) {
    echo "Paso {$step['step']}: {$step['page']}<br>";
    echo "Sesiones: {$step['sessions']}<br>";
    if (isset($step['drop_off'])) {
        echo "Abandono: {$step['drop_off']}%<br>";
    }
    echo "<br>";
}
```

---

## Cron Jobs

### Script de Mantenimiento

```php
// application/controllers/cli/Analytics_maintenance.php
<?php
class Analytics_maintenance extends CI_Controller {
    
    public function daily() {
        $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
        
        // Calcular estadísticas de ayer
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->analytics->calculate_daily_stats($yesterday);
        
        echo "Daily stats calculated for $yesterday\n";
    }
    
    public function cleanup() {
        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        
        // Limpiar sesiones de más de 60 días
        $deleted = $this->tracker->clean_old_sessions(60);
        
        echo "Deleted $deleted old sessions\n";
    }
}
```

### Configurar Crontab

```bash
# Calcular stats diarias a las 2 AM
0 2 * * * cd /path/to/project && php index.php cli/analytics_maintenance daily

# Limpiar sesiones viejas semanalmente (domingos a las 3 AM)
0 3 * * 0 cd /path/to/project && php index.php cli/analytics_maintenance cleanup
```

---

## Consejos de Optimización

1. **Usa las vistas SQL** para consultas frecuentes:
```php
$this->db->query("SELECT * FROM v_user_tracking_analytics WHERE visit_date >= ?", [date('Y-m-d', strtotime('-30 days'))]);
```

2. **Cachea resultados costosos**:
```php
$this->load->driver('cache');

$cache_key = 'analytics_overview_30d';
$stats = $this->cache->get($cache_key);

if (!$stats) {
    $stats = $this->analytics->get_overview_stats(/* ... */);
    $this->cache->save($cache_key, $stats, 3600); // 1 hora
}
```

3. **Usa índices** para filtros frecuentes
4. **Ejecuta cálculos pesados** con cron jobs
5. **Limpia datos antiguos** regularmente

---

¡Listo! Con estos ejemplos puedes implementar analytics completos en tu aplicación.
