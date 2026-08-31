# Admin Navbar Contextual

> **Estado (Start CMS 3.0):** shipped. Guía de la barra de admin inyectada en el sitio público (`shared.admin_navbar`, `public/js/admin-navbar.js`).

El admin navbar es **contextual**. Detecta qué está viendo el editor logueado y muestra opciones relevantes.

## 🎯 Características

### 1. **Detección Automática**
El navbar detecta:
- **Páginas**: Muestra título, template y estado
- **Posts de Blog**: Muestra título del post
- **Formularios**: Detecta si hay un formulario en la página actual
- **Contexto actual**: URL y path para acciones

### 2. **Menús Contextuales**

#### **Menú de Página** (aparece cuando estás en una página)
```
📄 Página
├── Editar Página
├── Vista Previa
├── Ver Estadísticas (si tienes permisos)
└── Limpiar Caché
```

#### **Menú de Blog** (aparece cuando estás en un post)
```
📰 Post  
├── Editar Post
├── Ver Estadísticas (si tienes permisos)
└── Gestionar Categorías
```

#### **Menú de Formulario** (aparece si hay un formulario en la página)
```
📋 Formulario
├── Ver Envíos
├── Editar Formulario
└── Exportar Datos
```

#### **Acciones Rápidas** (siempre disponible)
```
⚡ Acciones
├── Limpiar Todo el Caché
├── Toggle Debug
├── Ver en Frontend
├── Configuración SEO (si tienes permisos)
└── Backups
```

## 🛠️ Funciones JavaScript Disponibles

```javascript
// API global en window.scmsAdminBar

// Limpiar caché de una página
scmsAdminBar.clearPageCache(pageId);

// Limpiar todo el caché
scmsAdminBar.clearAllCache();

// Activar/desactivar modo debug
scmsAdminBar.toggleDebug();

// Exportar datos de formulario
scmsAdminBar.exportFormData('contacto');

// Cargar notificaciones
scmsAdminBar.loadNotifications();
```

## 📦 Archivos Modificados

1. **`application/views/shared/admin_navbar.blade.php`** (565 líneas)
   - Variables de contexto ampliadas
   - 4 menús contextuales (Página, Blog, Formulario, Acciones)
   - Estilos CSS con clase `.scms-context-item` para resaltar
   - JavaScript con 5 funciones exportadas

2. **`application/helpers/adminbar_helper.php`** (nuevo)
   - `detect_page_forms()` - Detecta formularios
   - `get_page_menus($page_id)` - Obtiene menús
   - `get_page_analytics_summary($page_id)` - Estadísticas 30 días
   - `get_current_page_seo_score($page)` - Calcula SEO score
   - `get_page_last_editor($page_id)` - Último editor
   - `check_page_cache_status($page_id)` - Estado de caché

## 🚀 Próximos Pasos Recomendados

### Implementar Endpoints del Backend

Para que las funciones JavaScript trabajen, necesitas crear estos endpoints:

#### 1. **Cache Management**
```php
// application/controllers/admin/Cache.php (nuevo)
POST /admin/cache/clear-page/{page_id}
POST /admin/cache/clear-all
```

#### 2. **Debug Toggle**
```php
// application/controllers/admin/Config.php
POST /admin/config/toggle-debug
// Response: {"success": true, "debug_enabled": true}
```

#### 3. **Form Export**
```php
// application/controllers/admin/Siteforms.php
GET /admin/siteforms/export/{form_name}
// Descarga archivo CSV/Excel
```

## 💡 Ejemplo de Uso en Vista

Cuando renderizas una página en tu controlador:

```php
// En tu ThemeController
$data['page'] = $this->pages_model->find($page_id);

// El navbar detectará automáticamente:
// - $page->page_id
// - $page->page_title
// - $page->page_data (con template)
// - $page->status

echo $this->blade->view('site.home', $data);
```

Para un formulario:

```php
// En tu controlador
$data['siteform'] = $this->siteforms_model->find($form_name);

// El navbar detectará el formulario y mostrará el menú contextual
echo $this->blade->view('site.contacto', $data);
```

## 🎨 Personalización

Los items contextuales tienen un fondo azul claro para distinguirse:

```css
.scms-context-item {
    background: rgba(0, 176, 255, 0.1) !important;
}
```

Puedes modificar este color en [admin_navbar.blade.php](application/views/shared/admin_navbar.blade.php#L417).

## 🔐 Permisos

El navbar respeta todos los permisos:
- `UPDATE_PAGE` - Editar páginas
- `UPDATE_BLOG` - Editar blogs  
- `SELECT_ANALYTICS` - Ver estadísticas
- `SELECT_SITEFORMS` - Acceder a formularios
- `UPDATE_CONFIG` - Configuración SEO

## ✨ Mejoras Futuras Sugeridas

1. **Duplicar Página/Post** - Botón para clonar contenido
2. **SEO Score en Tiempo Real** - Mostrar puntuación directamente
3. **Usuarios Activos** - Ver quién más está editando
4. **Historial de Versiones** - Restaurar versiones anteriores
5. **Shortcuts de Teclado** - Atajos para acciones rápidas
6. **Notificaciones Push** - Alertas en tiempo real

---

**Última actualización**: 10 de enero de 2026  
**Versión**: 2.0 - Sistema Contextual Inteligente
