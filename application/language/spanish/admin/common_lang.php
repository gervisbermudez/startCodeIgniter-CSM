<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Common translations
$lang['save'] = 'Guardar';
$lang['cancel'] = 'Cancelar';
$lang['edit'] = 'Editar';
$lang['delete'] = 'Eliminar';
$lang['search'] = 'Buscar';
$lang['search_placeholder'] = 'Buscar...';
$lang['search_shortcut_hint'] = 'Ctrl K';
$lang['search_palette_placeholder'] = 'Buscar páginas, usuarios, archivos…';
$lang['search_palette_hint'] = 'Enter para abrir · Esc para cerrar';
$lang['search_view_all'] = 'Ver todos los resultados';
$lang['search_no_query'] = 'Busca en todo el panel';
$lang['search_no_results'] = 'Ningún resultado para "%s"';
$lang['search_empty_cta'] = 'Limpiar búsqueda';
$lang['search_min_chars'] = 'Escribe al menos 2 caracteres';
$lang['search_type_all'] = 'Todo';
$lang['search_type_pages'] = 'Páginas';
$lang['search_type_users'] = 'Usuarios';
$lang['search_type_files'] = 'Archivos';
$lang['search_type_albums'] = 'Álbumes';
$lang['search_type_categories'] = 'Categorías';
$lang['search_type_models'] = 'Modelos';
$lang['search_type_contents'] = 'Contenidos';
$lang['search_type_siteforms'] = 'Formularios';
$lang['search_type_submissions'] = 'Envíos';
$lang['search_type_menus'] = 'Menús';
$lang['search_results_title'] = 'Resultados de búsqueda';
$lang['search_results_count'] = '%s resultados';
$lang['search_error'] = 'Ocurrió un error inesperado';
$lang['search_status_published'] = 'Publicado';
$lang['search_status_draft'] = 'Borrador';
$lang['search_status_archived'] = 'Archivado';
$lang['search_status_deleted'] = 'Eliminado';
$lang['refresh'] = 'Actualizar';
$lang['toggle_view'] = 'Cambiar vista de tarjetas y tabla';
$lang['filter'] = 'Filtrar';
$lang['actions'] = 'Acciones';
$lang['options'] = 'Opciones';
$lang['accept'] = 'Aceptar';
$lang['create'] = 'Crear';
$lang['update'] = 'Actualizar';
$lang['view'] = 'Ver';

// User related
$lang['user'] = 'Usuario';
$lang['users'] = 'Usuarios';
$lang['username'] = 'Nombre de usuario';
$lang['password'] = 'Contraseña';
$lang['current_password'] = 'Actual Contraseña';
$lang['repeat_password'] = 'Repetir Contraseña';
$lang['email'] = 'Email';
$lang['name'] = 'Nombre';
$lang['first_name'] = 'Nombre';
$lang['last_name'] = 'Apellido';
$lang['user_data'] = 'Datos del usuario';

// Login/Session
$lang['login'] = 'Iniciar sesión';
$lang['logout'] = 'Cerrar sesión';
$lang['remember_me'] = 'Recordarme';
$lang['login_with_other_user'] = 'Ingresar con otro usuario';

// Date/Time
$lang['date'] = 'Fecha';
$lang['time'] = 'Hora';
$lang['publish_date'] = 'Fecha de publicación';
$lang['creation_date'] = 'Fecha de creación';

// Status
$lang['status'] = 'Estado';
$lang['active'] = 'Activo';
$lang['inactive'] = 'Inactivo';
$lang['not_active'] = 'No Activo';

// System
$lang['title'] = 'Título';
$lang['description'] = 'Descripción';
$lang['content'] = 'Contenido';
$lang['administration'] = 'Administración';
$lang['configuration'] = 'Configuración';
$lang['page'] = 'Página';
$lang['pages'] = 'Páginas';
$lang['home'] = 'Inicio';

// Messages
$lang['system_logs_cleaned'] = 'Se eliminaron @count logs de sistema';
$lang['error_logs_cleaned'] = 'Se eliminaron @count logs de errores';
$lang['user_tracking_cleaned'] = 'Se eliminaron @count tracks de usuarios';

$lang['analytics_dashboard'] = 'Panel de analíticas';
$lang['analytics_start_date'] = 'Fecha inicio';
$lang['analytics_end_date'] = 'Fecha fin';
$lang['analytics_apply_filter'] = 'Aplicar filtro';
$lang['analytics_export_csv'] = 'Exportar CSV';
$lang['analytics_total_sessions'] = 'Sesiones totales';
$lang['analytics_unique_visitors'] = 'Visitantes únicos';
$lang['analytics_total_pageviews'] = 'Vistas de página';
$lang['analytics_avg_time_on_page'] = 'Tiempo medio en página';
$lang['analytics_bounce_rate'] = 'Tasa de rebote';
$lang['analytics_conversion_rate'] = 'Tasa de conversión';
$lang['analytics_pages_per_session'] = 'Páginas por sesión';
$lang['analytics_traffic_trend'] = 'Tendencia de tráfico';
$lang['analytics_sessions'] = 'Sesiones';
$lang['analytics_pageviews'] = 'Vistas';
$lang['analytics_visits_by_device'] = 'Visitas por dispositivo';
$lang['analytics_top_pages'] = 'Top 10 páginas más visitadas';
$lang['analytics_popular_pages'] = 'Páginas populares';
$lang['analytics_page'] = 'Página';
$lang['analytics_visits'] = 'Visitas';
$lang['analytics_avg_time'] = 'Tiempo medio';
$lang['analytics_traffic_sources'] = 'Fuentes de tráfico';
$lang['analytics_source'] = 'Fuente';
$lang['analytics_type'] = 'Tipo';
$lang['analytics_conv_rate'] = 'Tasa conv.';
$lang['analytics_realtime'] = 'Visitantes en tiempo real (últimos 30 minutos)';
$lang['analytics_no_realtime'] = 'No hay visitantes activos en los últimos 30 minutos';
$lang['analytics_active_sessions'] = 'Sesiones activas';
$lang['analytics_mobile'] = 'Móvil';
$lang['analytics_desktop'] = 'Escritorio';
$lang['analytics_top_events'] = 'Eventos principales';
$lang['analytics_event_category'] = 'Categoría';
$lang['analytics_event_action'] = 'Acción';
$lang['analytics_event_count'] = 'Cantidad';
$lang['analytics_no_data'] = 'No hay datos para el período seleccionado';
$lang['analytics_filtering_page'] = 'Filtrando por página';
$lang['analytics_clear_filter'] = 'Quitar filtro';
$lang['analytics_unauthorized'] = 'Debes iniciar sesión para exportar';
$lang['analytics_head_code'] = 'Código en el head (snippet)';
$lang['analytics_ga4_placeholder'] = 'G-XXXXXXXX';
$lang['menu_analytics'] = 'Analíticas';
$lang['dashboard_view_analytics'] = 'Ver analíticas';
$lang['dashboard_unique_visitors'] = 'Visitantes únicos';
$lang['dashboard_today_visits'] = 'Visitas de hoy';
$lang['dashboard_pages_per_session'] = 'Páginas / sesión';
$lang['dashboard_bounce_rate'] = 'Tasa de rebote';
$lang['dashboard_vs_yesterday'] = 'vs ayer';
$lang['dashboard_yesterday'] = 'Ayer';
$lang['dashboard_engagement'] = 'Engagement';
$lang['dashboard_bounce_good'] = 'Bien';
$lang['dashboard_bounce_improve'] = 'Hay que mejorar';
$lang['dashboard_no_analytics_data'] = 'No hay visitas en los últimos 30 días';
$lang['dashboard_visitors'] = 'Visitantes';
$lang['dashboard_requests'] = 'Vistas';
$lang['dashboard_growth'] = 'Crecimiento';
$lang['dashboard_top_pages'] = 'Páginas más visitadas';
$lang['dashboard_traffic_sources'] = 'Fuentes de tráfico';
$lang['dashboard_tracking_disabled'] = 'El tracking propio está apagado. Activá “Track visitors” en configuración para registrar pageviews.';
$lang['dashboard_enable_tracking'] = 'Abrir configuración';
$lang['help_avoid_spam'] = 'Ayudanos a evitar en spam';
$lang['please_verify_email'] = 'por favor, verifica tu email';

// Validation messages
$lang['valid'] = 'Valid';
$lang['password_requirements'] = 'Debe contener mayusculas, minuculas, numeros y un caracter especial';
$lang['and'] = 'y';

// Additional fields
$lang['category'] = 'Categoría';
$lang['subcategory'] = 'Subcategoría';
$lang['template'] = 'Plantilla';
$lang['type'] = 'Tipo';
$lang['none'] = 'Ninguna';
$lang['published'] = 'Publicado';
$lang['draft'] = 'Borrador';
$lang['select'] = 'Seleccionar';
$lang['public'] = 'Público';
$lang['private'] = 'Privado';
$lang['activity'] = 'Actividad';
$lang['user_type'] = 'Tipo de usuario';
$lang['profile_information'] = 'Información del perfil';
$lang['publish_immediately'] = 'Publicar inmediatamente';
$lang['select_date_and_time'] = 'Seleccionar fecha y hora';
$lang['activate_site_form'] = 'Activar Site Form';
$lang['filters'] = 'Filtros';
$lang['albums'] = 'Álbumes';
$lang['not_published'] = 'No publicado';
$lang['publish_album'] = 'Publicar Album';
$lang['add_images'] = 'Agregar Imagenes';
$lang['no_configurations'] = 'No hay Configuraciones';
$lang['last_maintenance'] = 'Último mantenimiento realizado';
$lang['loading_system_configs'] = 'Cargando configuraciones de sistema';
$lang['archive'] = 'Archivo';
$lang['upload_file'] = 'Subir Archivo';
$lang['your_files'] = 'Tus Archivos';
$lang['search_files'] = 'Buscar Archivos';
$lang['no_files'] = 'No hay archivos';
$lang['upload_files'] = 'Subir Archivos';
