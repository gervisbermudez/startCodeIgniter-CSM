@if(userdata('logged_in'))
<?php 
// Obtener información de la página actual si existe
$current_page_id = isset($page->page_id) ? $page->page_id : null;
$current_page_title = isset($page->page_title) ? $page->page_title : null;
$current_blog_id = isset($blog->blog_id) ? $blog->blog_id : null;
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
            <ul id="scms-admin-panel-dropdown" class="dropdown-content scms-adminbar-dropdown">
                <li><a href="{{ base_url('admin') }}"><i class="material-icons scms-adminbar-icon">dashboard</i>Dashboard</a></li>
                @if(has_permisions('SELECT_PAGES'))
                <li><a href="{{ base_url('admin/pages') }}"><i class="material-icons scms-adminbar-icon">description</i>Páginas</a></li>
                @endif
                @if(has_permisions('SELECT_BLOG'))
                <li><a href="{{ base_url('admin/blogs') }}"><i class="material-icons scms-adminbar-icon">article</i>Blog</a></li>
                @endif
                @if(has_permisions('SELECT_USERS'))
                <li><a href="{{ base_url('admin/users') }}"><i class="material-icons scms-adminbar-icon">people</i>Usuarios</a></li>
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
            <ul id="scms-new-content-dropdown" class="dropdown-content scms-adminbar-dropdown">
                @if(has_permisions('CREATE_PAGE'))
                <li><a href="{{ base_url('admin/pages/add') }}"><i class="material-icons scms-adminbar-icon">description</i>Página</a></li>
                @endif
                @if(has_permisions('CREATE_BLOG'))
                <li><a href="{{ base_url('admin/blogs/add') }}"><i class="material-icons scms-adminbar-icon">article</i>Post</a></li>
                @endif
                @if(has_permisions('CREATE_USER'))
                <li><a href="{{ base_url('admin/users/add') }}"><i class="material-icons scms-adminbar-icon">person_add</i>Usuario</a></li>
                @endif
                @if(has_permisions('SELECT_GALLERY'))
                <li><a href="{{ base_url('admin/gallery') }}"><i class="material-icons scms-adminbar-icon">photo_library</i>Media</a></li>
                @endif
            </ul>
        </div>
        
        <!-- Right Side - User Info & Actions -->
        <div class="scms-adminbar-right">
            <!-- Current Page Edit Link -->
            <?php if ($current_page_id && has_permisions('UPDATE_PAGE')): ?>
            <div class="scms-adminbar-item scms-hide-on-small">
                <a href="{{ base_url('admin/pages/edit/' . $current_page_id) }}" class="scms-adminbar-link" title="Editar: <?php echo $current_page_title; ?>">
                    <i class="material-icons scms-adminbar-icon">edit</i>
                    <span class="scms-adminbar-text">Editar Página</span>
                </a>
            </div>
            <?php elseif ($current_blog_id && has_permisions('UPDATE_BLOG')): ?>
            <div class="scms-adminbar-item scms-hide-on-small">
                <a href="{{ base_url('admin/blogs/edit/' . $current_blog_id) }}" class="scms-adminbar-link" title="Editar post">
                    <i class="material-icons scms-adminbar-icon">edit</i>
                    <span class="scms-adminbar-text">Editar Post</span>
                </a>
            </div>
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
                <li><a href="{{ base_url('admin/notifications') }}"><i class="material-icons scms-adminbar-icon">list</i>Ver todas</a></li>
            </ul>
            
            <!-- User Menu -->
            <div class="scms-adminbar-item dropdown-trigger" data-target="scms-user-menu-dropdown">
                <a href="#!" class="scms-adminbar-link scms-adminbar-user">
                    @if(userdata('avatar'))
                    <img src="{{ base_url('uploads/' . userdata('avatar')) }}" alt="Avatar" class="scms-adminbar-avatar">
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
                <li><a href="{{ base_url('admin/users/edit/' . userdata('user_id')) }}"><i class="material-icons scms-adminbar-icon">person</i>Mi Perfil</a></li>
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
(function() {
    'use strict';
    document.addEventListener('DOMContentLoaded', function() {
        var scmsDropdowns = document.querySelectorAll('#scms-wp-adminbar .dropdown-trigger');
        if (scmsDropdowns.length > 0 && typeof M !== 'undefined') {
            M.Dropdown.init(scmsDropdowns, {
                alignment: 'right',
                constrainWidth: false,
                coverTrigger: false,
                closeOnClick: true,
                hover: false
            });
        }
        document.body.classList.add('scms-has-admin-bar');
        var fixedNavbar = document.querySelector('.navbar.fixed-top');
        if (fixedNavbar) {
            fixedNavbar.style.top = '46px';
        }
        var logoutLink = document.getElementById('scms-admin-bar-logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                    window.location.href = this.href;
                }
            });
        }
        scmsLoadNotifications();
    });
    function scmsLoadNotifications() {
        var badge = document.getElementById('scms-notification-count');
        if (badge) {
            // Example: badge.textContent = '3'; badge.style.display = 'block';
        }
    }
    window.scmsAdminBar = {
        loadNotifications: scmsLoadNotifications
    };
})();
</script>
@endif
