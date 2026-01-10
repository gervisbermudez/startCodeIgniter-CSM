@if(userdata('logged_in'))
<?php 
// URL actual
$current_url = current_url();
$current_path = uri_string();

// Detectar si estamos en una página de blog
$is_blog_page = strpos($current_path, 'blog/') === 0 && $current_path !== 'blog' && !preg_match('/^blog\/(search|tag|author|categorie)/', $current_path);

// Obtener información de la página actual
$current_page_id = null;
$current_page_title = null;
$current_page_template = null;
$current_page_status = null;
$current_blog_id = null;
$current_blog_title = null;

if (isset($page)) {
    if ($is_blog_page) {
        // Es un blog individual - extraer datos de blog
        $current_blog_id = isset($page->page_id) ? $page->page_id : null;
        
        // Obtener título del blog
        if (isset($page->page_title)) {
            $current_blog_title = $page->page_title;
        } elseif (isset($page->title)) {
            $current_blog_title = $page->title;
        }
    } else {
        // Es una página normal
        $current_page_id = isset($page->page_id) ? $page->page_id : null;
        
        // Obtener título de la página
        if (isset($page->page_title)) {
            $current_page_title = $page->page_title;
        } elseif (isset($page->title)) {
            $current_page_title = $page->title;
        }
        
        if (isset($page->page_data)) {
            $page_data_obj = is_string($page->page_data) ? json_decode($page->page_data) : $page->page_data;
            $current_page_template = isset($page_data_obj->template) ? $page_data_obj->template : null;
            $current_page_status = isset($page->status) ? $page->status : null;
        }
    }
}

// Detectar formulario en la página
$has_form = isset($siteform) && $siteform;
$form_name = $has_form ? (isset($siteform->form_name) ? $siteform->form_name : null) : null;
?>
<!-- Admin Bar - WordPress Style with MaterializeCSS -->
<div id="scms-wp-adminbar" class="scms-wp-adminbar">
    <div class="scms-adminbar-inner">
        <!-- Left Side - Site Info & Navigation -->
        <div class="scms-adminbar-left">
            <!-- Site Menu -->
            <div class="scms-adminbar-item">
                <a href="{{ base_url() }}" class="scms-adminbar-logo" title="Visitar sitio">
                    <i class="material-icons scms-adminbar-icon">home</i>
                    <span class="scms-adminbar-text scms-hide-on-small">{{ config('SITE_TITLE') }}</span>
                </a>
            </div>
            
            <!-- Admin Panel Link -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-admin-panel-dropdown">
                <a href="#!" class="scms-adminbar-link">
                    <i class="material-icons scms-adminbar-icon">dashboard</i>
                    <span class="scms-adminbar-text scms-hide-on-small">Panel Admin</span>
                    <i class="material-icons scms-adminbar-icon scms-adminbar-arrow">arrow_drop_down</i>
                </a>
            </div>
            
            <!-- Dropdown: Admin Panel -->
            <ul id="scms-admin-panel-dropdown" class="dropdown-content scms-adminbar-dropdown scms-admin-panel-menu">
                <li class="scms-dropdown-header">Panel de Administración</li>
                <li class="divider scms-adminbar-divider"></li>
                @if(has_permisions('SELECT_PAGES'))
                <li><a href="{{ base_url('admin/pages') }}"><i class="material-icons scms-adminbar-icon">description</i>Páginas</a></li>
                @endif
                @if(has_permisions('SELECT_PAGES'))
                <li><a href="{{ base_url('admin/pages?type=blog') }}"><i class="material-icons scms-adminbar-icon">article</i>Blog</a></li>
                @endif
                @if(has_permisions('SELECT_USERS'))
                <li><a href="{{ base_url('admin/users') }}"><i class="material-icons scms-adminbar-icon">people</i>Usuarios</a></li>
                @endif
                @if(has_permisions('SELECT_SITEFORMS'))
                <li><a href="{{ base_url('admin/siteforms') }}"><i class="material-icons scms-adminbar-icon">assignment</i>Formularios</a></li>
                @endif
                @if(has_permisions('SELECT_MENU'))
                <li><a href="{{ base_url('admin/menus') }}"><i class="material-icons scms-adminbar-icon">menu</i>Menús</a></li>
                @endif
                @if(has_permisions('SELECT_GALLERY'))
                <li><a href="{{ base_url('admin/gallery') }}"><i class="material-icons scms-adminbar-icon">photo_library</i>Galería</a></li>
                @endif
                @if(has_permisions('SELECT_ANALYTICS'))
                <li><a href="{{ base_url('admin/analytics') }}"><i class="material-icons scms-adminbar-icon">analytics</i>Analíticas</a></li>
                @endif
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="{{ base_url('admin/config') }}"><i class="material-icons scms-adminbar-icon">settings</i>Configuración</a></li>
            </ul>
            
            <!-- New Content Link -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-new-content-dropdown">
                <a href="#!" class="scms-adminbar-link">
                    <i class="material-icons scms-adminbar-icon">add_circle</i>
                    <span class="scms-adminbar-text scms-hide-on-small">Nuevo</span>
                    <i class="material-icons scms-adminbar-icon scms-adminbar-arrow">arrow_drop_down</i>
                </a>
            </div>
            
            <!-- Dropdown: New Content -->
            <ul id="scms-new-content-dropdown" class="dropdown-content scms-adminbar-dropdown scms-new-content-menu">
                <li class="scms-dropdown-header">Crear Nuevo Contenido</li>
                <li class="divider scms-adminbar-divider"></li>
                @if(has_permisions('CREATE_PAGE'))
                <li><a href="{{ base_url('admin/pages/new') }}"><i class="material-icons scms-adminbar-icon">description</i>Página</a></li>
                @endif
                @if(has_permisions('CREATE_BLOG'))
                <li><a href="{{ base_url('admin/pages/add?type=blog') }}"><i class="material-icons scms-adminbar-icon">article</i>Post de Blog</a></li>
                @endif
                @if(has_permisions('CREATE_USER'))
                <li><a href="{{ base_url('admin/users/add') }}"><i class="material-icons scms-adminbar-icon">person_add</i>Usuario</a></li>
                @endif
                @if(has_permisions('SELECT_GALLERY'))
                <li><a href="{{ base_url('admin/gallery') }}"><i class="material-icons scms-adminbar-icon">photo_library</i>Media</a></li>
                @endif
                @if(has_permisions('SELECT_MENU'))
                <li><a href="{{ base_url('admin/menus/new') }}"><i class="material-icons scms-adminbar-icon">menu</i>Menú</a></li>
                @endif
                @if(has_permisions('SELECT_SITEFORMS'))
                <li><a href="{{ base_url('admin/siteforms/new') }}"><i class="material-icons scms-adminbar-icon">assignment</i>Formulario</a></li>
                @endif
            </ul>
        </div>
        
        <!-- Right Side - User Info & Actions -->
        <div class="scms-adminbar-right">
            <!-- DIRECT EDIT BUTTON for Current Page/Blog -->
            <?php if ($current_page_id && has_permisions('UPDATE_PAGE')): ?>
            <div class="scms-adminbar-item">
                <a href="{{ base_url('admin/pages/edit/' . $current_page_id) }}" class="scms-adminbar-link scms-adminbar-edit-btn" title="Editar: <?php echo $current_page_title; ?>">
                    <i class="material-icons scms-adminbar-icon">edit</i>
                    <span class="scms-adminbar-text scms-hide-on-small">Editar Página</span>
                </a>
            </div>
            
            <!-- Context Menu: More Page Actions -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-page-more-dropdown">
                <a href="#!" class="scms-adminbar-link">
                    <i class="material-icons scms-adminbar-icon">more_vert</i>
                </a>
            </div>
            
            <ul id="scms-page-more-dropdown" class="dropdown-content scms-adminbar-dropdown">
                <li class="scms-dropdown-header scms-context-item">
                    <strong><?php echo $current_page_title ?: 'Página'; ?></strong><br>
                    <small><?php echo $current_page_template ?: 'default'; ?></small>
                </li>
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="#!" onclick="scmsAdminBar.copyPageUrl('{{ current_url() }}'); return false;"><i class="material-icons scms-adminbar-icon">link</i>Copiar Enlace</a></li>
                <li><a href="#!" onclick="scmsAdminBar.duplicatePage(<?php echo $current_page_id; ?>, '<?php echo addslashes($current_page_title); ?>'); return false;"><i class="material-icons scms-adminbar-icon">content_copy</i>Duplicar Página</a></li>
                @if(has_permisions('SELECT_ANALYTICS'))
                <li><a href="{{ base_url('admin/analytics?page_id=' . $current_page_id) }}"><i class="material-icons scms-adminbar-icon">analytics</i>Ver Estadísticas</a></li>
                @endif
                <li class="divider scms-adminbar-divider"></li>
                <li class="scms-toggle-item">
                    <label class="scms-toggle-label">
                        <span><i class="material-icons scms-adminbar-icon">visibility</i>Publicada</span>
                        <div class="scms-toggle-switch" data-page-id="<?php echo $current_page_id; ?>" data-action="toggle-visibility">
                            <input type="checkbox" <?php echo ($current_page_status == '1' || $current_page_status === 'publish' || $current_page_status === 'active') ? 'checked' : ''; ?>>
                            <span class="scms-toggle-slider"></span>
                        </div>
                    </label>
                </li>
                @if(has_permisions('DELETE_PAGE'))
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="#!" onclick="scmsAdminBar.archivePage(<?php echo $current_page_id; ?>, '<?php echo addslashes($current_page_title); ?>'); return false;" class="scms-danger-action"><i class="material-icons scms-adminbar-icon">archive</i>Archivar Página</a></li>
                @endif
            </ul>
            
            <?php elseif ($current_blog_id && has_permisions('UPDATE_PAGE')): ?>
            <!-- DIRECT EDIT BUTTON for Blog Post -->
            <div class="scms-adminbar-item">
                <a href="{{ base_url('admin/pages/edit/' . $current_blog_id) }}" class="scms-adminbar-link scms-adminbar-edit-btn" title="Editar: <?php echo $current_blog_title; ?>">
                    <i class="material-icons scms-adminbar-icon">edit</i>
                    <span class="scms-adminbar-text scms-hide-on-small">Editar Post</span>
                </a>
            </div>
            
            <!-- Context Menu: More Blog Actions -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-blog-more-dropdown">
                <a href="#!" class="scms-adminbar-link">
                    <i class="material-icons scms-adminbar-icon">more_vert</i>
                </a>
            </div>
            
            <ul id="scms-blog-more-dropdown" class="dropdown-content scms-adminbar-dropdown">
                <li class="scms-dropdown-header scms-context-item">
                    <strong><?php echo $current_blog_title ?: 'Blog Post'; ?></strong><br>
                    <small>Blog</small>
                </li>
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="#!" onclick="scmsAdminBar.copyPageUrl('{{ current_url() }}'); return false;"><i class="material-icons scms-adminbar-icon">link</i>Copiar Enlace</a></li>
                <li><a href="#!" onclick="scmsAdminBar.duplicatePage(<?php echo $current_blog_id; ?>, '<?php echo addslashes($current_blog_title); ?>'); return false;"><i class="material-icons scms-adminbar-icon">content_copy</i>Duplicar Post</a></li>
                @if(has_permisions('SELECT_ANALYTICS'))
                <li><a href="{{ base_url('admin/analytics?page_id=' . $current_blog_id) }}"><i class="material-icons scms-adminbar-icon">analytics</i>Ver Estadísticas</a></li>
                @endif
                <li class="divider scms-adminbar-divider"></li>
                <li class="scms-toggle-item">
                    <label class="scms-toggle-label">
                        <span><i class="material-icons scms-adminbar-icon">visibility</i>Publicado</span>
                        <div class="scms-toggle-switch" data-page-id="<?php echo $current_blog_id; ?>" data-action="toggle-visibility">
                            <input type="checkbox" checked>
                            <span class="scms-toggle-slider"></span>
                        </div>
                    </label>
                </li>
                @if(has_permisions('DELETE_PAGE'))
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="#!" onclick="scmsAdminBar.archivePage(<?php echo $current_blog_id; ?>, '<?php echo addslashes($current_blog_title); ?>'); return false;" class="scms-danger-action"><i class="material-icons scms-adminbar-icon">archive</i>Archivar Post</a></li>
                @endif
            </ul>
            <?php endif; ?>
            
            <!-- Form Actions (if form present) -->
            <?php if ($has_form && has_permisions('SELECT_SITEFORMS')): ?>
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-form-dropdown">
                <a href="#!" class="scms-adminbar-link scms-adminbar-highlight">
                    <i class="material-icons scms-adminbar-icon">assignment</i>
                    <span class="scms-adminbar-text scms-hide-on-small"><?php echo $form_name; ?></span>
                    <i class="material-icons scms-adminbar-icon scms-adminbar-arrow">arrow_drop_down</i>
                </a>
            </div>
            
            <ul id="scms-form-dropdown" class="dropdown-content scms-adminbar-dropdown">
                <li class="scms-dropdown-header">Formulario: <?php echo $form_name; ?></li>
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="{{ base_url('admin/siteforms/data?form=' . urlencode($form_name)) }}"><i class="material-icons scms-adminbar-icon">inbox</i>Ver Envíos</a></li>
                <li><a href="{{ base_url('admin/siteforms/edit/' . urlencode($form_name)) }}"><i class="material-icons scms-adminbar-icon">edit</i>Editar Formulario</a></li>
                <li><a href="#!" onclick="scmsAdminBar.exportFormData('<?php echo $form_name; ?>')"><i class="material-icons scms-adminbar-icon">download</i>Exportar Datos</a></li>
                <li class="divider scms-adminbar-divider"></li>
                <li class="scms-toggle-item">
                    <label class="scms-toggle-label">
                        <span><i class="material-icons scms-adminbar-icon">notifications_active</i>Notificar</span>
                        <div class="scms-toggle-switch" data-form-name="<?php echo $form_name; ?>" data-action="toggle-notifications">
                            <input type="checkbox" checked>
                            <span class="scms-toggle-slider"></span>
                        </div>
                    </label>
                </li>
                <li class="scms-toggle-item">
                    <label class="scms-toggle-label">
                        <span><i class="material-icons scms-adminbar-icon">shield</i>CAPTCHA</span>
                        <div class="scms-toggle-switch" data-form-name="<?php echo $form_name; ?>" data-action="toggle-captcha">
                            <input type="checkbox">
                            <span class="scms-toggle-slider"></span>
                        </div>
                    </label>
                </li>
            </ul>
            <?php endif; ?>
            
            <!-- Notifications -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-notifications-dropdown">
                <a href="#!" class="scms-adminbar-link scms-adminbar-notifications">
                    <i class="material-icons scms-adminbar-icon">notifications</i>
                    <span class="scms-adminbar-badge" id="scms-notification-count" style="display: none;">0</span>
                </a>
            </div>
            
            <!-- Dropdown: Notifications -->
            <ul id="scms-notifications-dropdown" class="dropdown-content scms-adminbar-dropdown scms-notifications-dropdown">
                <li class="scms-dropdown-header">Notificaciones</li>
                <li class="divider scms-adminbar-divider"></li>
                <li id="scms-no-notifications"><a href="#!"><i class="material-icons scms-adminbar-icon">info</i>No hay notificaciones nuevas</a></li>
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="{{ base_url('admin') }}"><i class="material-icons scms-adminbar-icon">list</i>Ver todas</a></li>
            </ul>
            
            <!-- User Menu -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-user-menu-dropdown">
                <a href="#!" class="scms-adminbar-link scms-adminbar-user">
                    @if(userdata('avatar'))
                    <img src="{{ base_url(userdata('avatar')) }}" alt="Avatar" class="scms-adminbar-avatar">
                    @else
                    <i class="material-icons scms-adminbar-icon">account_circle</i>
                    @endif
                    <span class="scms-adminbar-text scms-hide-on-small">{{ userdata('username') }}</span>
                    <i class="material-icons scms-adminbar-icon scms-adminbar-arrow">arrow_drop_down</i>
                </a>
            </div>
            
            <!-- Dropdown: User Menu -->
            <ul id="scms-user-menu-dropdown" class="dropdown-content scms-adminbar-dropdown">
                <li class="scms-dropdown-header">
                    <strong>{{ userdata('username') }}</strong><br>
                    <small>{{ userdata('role') }}</small>
                </li>
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="{{ base_url('admin/users/ver/' . userdata('user_id')) }}"><i class="material-icons scms-adminbar-icon">person</i>Mi Perfil</a></li>
                <li><a href="{{ base_url('admin') }}"><i class="material-icons scms-adminbar-icon">dashboard</i>Dashboard</a></li>
                <li class="divider scms-adminbar-divider"></li>
                <li><a href="{{ base_url('api/v1/login/logout') }}" id="scms-admin-bar-logout"><i class="material-icons scms-adminbar-icon">exit_to_app</i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</div>

<style>
/* SCMS Admin Bar - Highly Specific Styles to Prevent Theme Conflicts */
#scms-wp-adminbar.scms-wp-adminbar {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 46px !important;
    background: #23282d !important;
    z-index: 99999 !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    line-height: normal !important;
}

.scms-wp-adminbar .scms-adminbar-inner {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    height: 100% !important;
    padding: 0 10px !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}

.scms-wp-adminbar .scms-adminbar-left,
.scms-wp-adminbar .scms-adminbar-right {
    display: flex !important;
    align-items: center !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.scms-wp-adminbar .scms-adminbar-item {
    position: relative !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    margin: 0 !important;
    padding: 0 !important;
}

.scms-wp-adminbar .scms-adminbar-link,
.scms-wp-adminbar .scms-adminbar-logo {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 0 12px !important;
    height: 100% !important;
    color: rgba(240, 246, 252, 0.7) !important;
    text-decoration: none !important;
    font-size: 13px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    border: none !important;
    background: transparent !important;
    margin: 0 !important;
    line-height: normal !important;
    font-weight: normal !important;
    text-transform: none !important;
    letter-spacing: normal !important;
}

.scms-wp-adminbar .scms-adminbar-link:hover,
.scms-wp-adminbar .scms-adminbar-logo:hover {
    background: #32373c !important;
    color: #00b0ff !important;
    text-decoration: none !important;
}

.scms-wp-adminbar .scms-adminbar-logo {
    font-weight: 600 !important;
    color: rgba(240, 246, 252, 0.9) !important;
}

/* Botón de edición sutil */
.scms-wp-adminbar .scms-adminbar-edit-btn {
    color: #00b0ff !important;
    font-weight: 500 !important;
    padding: 0 12px !important;
    border-left: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.scms-wp-adminbar .scms-adminbar-edit-btn:hover {
    background: rgba(0, 176, 255, 0.1) !important;
    color: #00d4ff !important;
}

/* Highlight para formularios */
.scms-wp-adminbar .scms-adminbar-highlight {
    background: rgba(255, 152, 0, 0.15) !important;
}

.scms-wp-adminbar .scms-adminbar-highlight:hover {
    background: rgba(255, 152, 0, 0.25) !important;
    color: #ffa726 !important;
}

.scms-wp-adminbar .scms-adminbar-icon {
    font-size: 18px !important;
    line-height: 1 !important;
    vertical-align: middle !important;
}

.scms-wp-adminbar .scms-adminbar-arrow {
    font-size: 16px !important;
}

.scms-wp-adminbar .scms-adminbar-text {
    display: inline !important;
    font-size: 13px !important;
    line-height: normal !important;
}

.scms-wp-adminbar .scms-adminbar-avatar {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    display: inline-block !important;
    vertical-align: middle !important;
}

.scms-wp-adminbar .scms-adminbar-notifications {
    position: relative !important;
}

.scms-wp-adminbar .scms-adminbar-badge {
    position: absolute !important;
    top: 8px !important;
    right: 8px !important;
    background: #ff4444 !important;
    color: white !important;
    font-size: 10px !important;
    font-weight: bold !important;
    padding: 2px 5px !important;
    border-radius: 10px !important;
    min-width: 18px !important;
    text-align: center !important;
    line-height: 1.4 !important;
}

ul.dropdown-content.scms-adminbar-dropdown {
    margin-top: 0 !important;
    background: #23282d !important;
    border-radius: 0 !important;
    box-shadow: 0 3px 8px rgba(0,0,0,0.3) !important;
    min-width: 200px !important;
    padding: 0 !important;
    border: none !important;
}

ul.dropdown-content.scms-adminbar-dropdown li {
    min-height: 40px !important;
    background: transparent !important;
    margin: 0 !important;
    padding: 0 !important;
    list-style: none !important;
}

ul.dropdown-content.scms-adminbar-dropdown li > a {
    color: rgba(240, 246, 252, 0.7) !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 10px 16px !important;
    font-size: 13px !important;
    text-decoration: none !important;
    background: transparent !important;
    border: none !important;
    margin: 0 !important;
    line-height: normal !important;
    font-weight: normal !important;
    text-transform: none !important;
}

ul.dropdown-content.scms-adminbar-dropdown li > a:hover {
    background: #32373c !important;
    color: #00b0ff !important;
    text-decoration: none !important;
}

ul.dropdown-content.scms-adminbar-dropdown li > a .scms-adminbar-icon {
    font-size: 18px !important;
}

ul.dropdown-content.scms-adminbar-dropdown li.divider.scms-adminbar-divider {
    background: rgba(255,255,255,0.1) !important;
    margin: 4px 0 !important;
    height: 1px !important;
    min-height: 1px !important;
}

ul.dropdown-content.scms-adminbar-dropdown li.scms-dropdown-header {
    color: rgba(240, 246, 252, 0.9) !important;
    padding: 12px 16px !important;
    font-size: 12px !important;
    border-bottom: 1px solid rgba(255,255,255,0.1) !important;
    background: transparent !important;
}

ul.dropdown-content.scms-adminbar-dropdown.scms-notifications-dropdown {
    min-width: 280px !important;
}

ul.dropdown-content.scms-adminbar-dropdown.scms-admin-panel-menu,
ul.dropdown-content.scms-adminbar-dropdown.scms-new-content-menu {
    min-width: 240px !important;
}

ul.dropdown-content.scms-adminbar-dropdown li.scms-context-item {
    background: rgba(0, 176, 255, 0.1) !important;
}

/* Toggle Switches en Dropdowns */
ul.dropdown-content.scms-adminbar-dropdown li.scms-toggle-item {
    padding: 8px 16px !important;
    min-height: 48px !important;
}

ul.dropdown-content.scms-adminbar-dropdown li.scms-toggle-item .scms-toggle-label {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    cursor: pointer !important;
    margin: 0 !important;
    color: rgba(240, 246, 252, 0.7) !important;
    font-size: 13px !important;
}

ul.dropdown-content.scms-adminbar-dropdown li.scms-toggle-item .scms-toggle-label span {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

ul.dropdown-content.scms-adminbar-dropdown li.scms-toggle-item:hover {
    background: #32373c !important;
}

/* Toggle Switch Component */
.scms-toggle-switch {
    position: relative !important;
    display: inline-block !important;
    width: 40px !important;
    height: 20px !important;
    margin-left: auto !important;
}

.scms-toggle-switch input {
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
}

.scms-toggle-slider {
    position: absolute !important;
    cursor: pointer !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background-color: rgba(255, 255, 255, 0.2) !important;
    transition: 0.3s !important;
    border-radius: 20px !important;
}

.scms-toggle-slider:before {
    position: absolute !important;
    content: "" !important;
    height: 14px !important;
    width: 14px !important;
    left: 3px !important;
    bottom: 3px !important;
    background-color: white !important;
    transition: 0.3s !important;
    border-radius: 50% !important;
}

.scms-toggle-switch input:checked + .scms-toggle-slider {
    background-color: #00b0ff !important;
}

.scms-toggle-switch input:checked + .scms-toggle-slider:before {
    transform: translateX(20px) !important;
}

.scms-toggle-switch:hover .scms-toggle-slider {
    background-color: rgba(255, 255, 255, 0.3) !important;
}

.scms-toggle-switch input:checked:hover + .scms-toggle-slider {
    background-color: #0097d6 !important;
}

/* Quick Edit Toggle en navbar */
.scms-adminbar-link.scms-quick-edit-toggle {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    padding-left: 12px !important;
    padding-right: 12px !important;
}

.scms-toggle-switch.scms-inline-toggle {
    margin-left: 0 !important;
}

/* Danger Action Styles */
.scms-adminbar-dropdown a.scms-danger-action {
    color: #ff5252 !important;
}

.scms-adminbar-dropdown a.scms-danger-action:hover {
    background: rgba(255, 82, 82, 0.1) !important;
    color: #ff1744 !important;
}

.scms-adminbar-dropdown a.scms-danger-action .material-icons {
    color: #ff5252 !important;
}

body.scms-has-admin-bar {
    padding-top: 46px !important;
}

@media only screen and (max-width: 600px) {
    #scms-wp-adminbar.scms-wp-adminbar {
        height: 46px !important;
    }
    body.scms-has-admin-bar {
        padding-top: 46px !important;
    }
    .scms-wp-adminbar .scms-adminbar-link {
        padding: 0 10px !important;
    }
}

@media only screen and (max-width: 992px) {
    .scms-wp-adminbar .scms-hide-on-small {
        display: none !important;
    }
}
</style>

<script>
// Variables de configuración para admin-navbar.js
window.SCMS_BASE_URL = '{{ base_url() }}';
window.SCMS_CURRENT_URL = '{{ current_url() }}';
</script>
<script src="{{ base_url('public/js/admin-navbar.js') }}"></script>
@endif
