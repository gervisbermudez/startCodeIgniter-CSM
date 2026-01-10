<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Bar Helper
 * Funciones auxiliares para el navbar de administración contextual
 */

if (!function_exists('detect_page_forms')) {
    /**
     * Detecta si hay formularios en la página actual
     * @return array|null Información del formulario o null
     */
    function detect_page_forms() {
        $CI =& get_instance();
        
        // Verificar si hay variable $siteform en la vista
        if (isset($CI->load->get_var('siteform'))) {
            return $CI->load->get_var('siteform');
        }
        
        return null;
    }
}

if (!function_exists('get_page_menus')) {
    /**
     * Obtiene los menús donde aparece una página
     * @param int $page_id ID de la página
     * @return array Lista de menús
     */
    function get_page_menus($page_id) {
        $CI =& get_instance();
        $CI->load->database();
        
        $query = $CI->db->query("
            SELECT DISTINCT m.menu_name, m.menu_id
            FROM menu_item mi
            JOIN menu m ON mi.menu_id = m.menu_id
            WHERE mi.page_id = ?
        ", [$page_id]);
        
        return $query->result();
    }
}

if (!function_exists('get_page_analytics_summary')) {
    /**
     * Obtiene un resumen de analytics para una página
     * @param int $page_id ID de la página
     * @return array Resumen de visitas, pageviews, tiempo promedio
     */
    function get_page_analytics_summary($page_id) {
        $CI =& get_instance();
        $CI->load->database();
        
        // Obtener estadísticas de los últimos 30 días
        $query = $CI->db->query("
            SELECT 
                COUNT(DISTINCT session_id) as visits,
                COUNT(*) as pageviews,
                AVG(time_on_page) as avg_time
            FROM user_tracking
            WHERE page_id = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ", [$page_id]);
        
        $result = $query->row_array();
        return [
            'visits' => $result['visits'] ?? 0,
            'pageviews' => $result['pageviews'] ?? 0,
            'avg_time' => round($result['avg_time'] ?? 0)
        ];
    }
}

if (!function_exists('get_current_page_seo_score')) {
    /**
     * Calcula el SEO score de una página
     * @param object $page Objeto de la página
     * @return array Score y issues
     */
    function get_current_page_seo_score($page) {
        $score = 100;
        $issues = [];
        
        // Verificar título
        if (empty($page->page_title) || strlen($page->page_title) < 30) {
            $score -= 20;
            $issues[] = 'Título muy corto (mínimo 30 caracteres)';
        } elseif (strlen($page->page_title) > 60) {
            $score -= 10;
            $issues[] = 'Título muy largo (máximo 60 caracteres)';
        }
        
        // Verificar meta description
        $page_data = is_string($page->page_data) ? json_decode($page->page_data) : $page->page_data;
        $meta_desc = $page_data->meta_description ?? '';
        
        if (empty($meta_desc)) {
            $score -= 25;
            $issues[] = 'Falta meta description';
        } elseif (strlen($meta_desc) < 120) {
            $score -= 15;
            $issues[] = 'Meta description muy corta (mínimo 120 caracteres)';
        }
        
        // Verificar keywords
        if (empty($page_data->keywords ?? '')) {
            $score -= 15;
            $issues[] = 'Faltan keywords';
        }
        
        // Verificar URL amigable
        if (empty($page->page_url) || strpos($page->page_url, '?') !== false) {
            $score -= 10;
            $issues[] = 'URL no es amigable';
        }
        
        // Verificar imagen destacada
        if (empty($page_data->featured_image ?? '')) {
            $score -= 10;
            $issues[] = 'Falta imagen destacada';
        }
        
        $status = 'excelente';
        if ($score < 50) $status = 'pobre';
        elseif ($score < 70) $status = 'regular';
        elseif ($score < 85) $status = 'bueno';
        
        return [
            'score' => max(0, $score),
            'issues' => $issues,
            'status' => $status
        ];
    }
}

if (!function_exists('get_page_last_editor')) {
    /**
     * Obtiene el último usuario que editó una página
     * @param int $page_id ID de la página
     * @return array|null Información del editor
     */
    function get_page_last_editor($page_id) {
        $CI =& get_instance();
        $CI->load->database();
        
        $query = $CI->db->query("
            SELECT u.username, u.email, sl.created_at
            FROM system_logger sl
            JOIN user u ON sl.user_id = u.user_id
            WHERE sl.table_name = 'pages'
            AND sl.record_id = ?
            AND sl.action IN ('update', 'insert')
            ORDER BY sl.created_at DESC
            LIMIT 1
        ", [$page_id]);
        
        return $query->row_array();
    }
}

if (!function_exists('check_page_cache_status')) {
    /**
     * Verifica si una página está en caché
     * @param int $page_id ID de la página
     * @return bool True si está cacheada
     */
    function check_page_cache_status($page_id) {
        $cache_key = 'page_' . $page_id;
        $cached = get_cached($cache_key);
        return !empty($cached);
    }
}
