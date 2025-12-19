<?php
/**
 * Cache Helper - Gestiona caching de datos para mejorar rendimiento
 * 
 * Soporta caching de:
 * - Configuración del sitio
 * - Fragmentos
 * - Templates
 */

if (!function_exists('get_cached')) {
    /**
     * Obtiene un valor del cache
     * 
     * @param string $key Clave del cache
     * @param mixed $default Valor por defecto si no existe en cache
     * @return mixed
     */
    function get_cached(string $key, $default = null)
    {
        $ci = &get_instance();
        
        // Usar CI_Cache si está disponible
        if (isset($ci->cache)) {
            $cached = $ci->cache->get($key);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        return $default;
    }
}

if (!function_exists('set_cached')) {
    /**
     * Almacena un valor en el cache
     * 
     * @param string $key Clave del cache
     * @param mixed $value Valor a cachear
     * @param int $ttl Tiempo de vida en segundos (0 = sin expiración)
     * @return bool
     */
    function set_cached(string $key, $value, int $ttl = 3600): bool
    {
        $ci = &get_instance();
        
        // Usar CI_Cache si está disponible
        if (isset($ci->cache)) {
            return $ci->cache->save($key, $value, $ttl);
        }
        
        return false;
    }
}

if (!function_exists('delete_cached')) {
    /**
     * Elimina un valor del cache
     * 
     * @param string $key Clave del cache
     * @return bool
     */
    function delete_cached(string $key): bool
    {
        $ci = &get_instance();
        
        // Usar CI_Cache si está disponible
        if (isset($ci->cache)) {
            return $ci->cache->delete($key);
        }
        
        return false;
    }
}

if (!function_exists('flush_cache')) {
    /**
     * Limpia todo el cache
     * 
     * @return bool
     */
    function flush_cache(): bool
    {
        $ci = &get_instance();
        
        // Usar CI_Cache si está disponible
        if (isset($ci->cache)) {
            return $ci->cache->clean();
        }
        
        return false;
    }
}

if (!function_exists('get_site_config')) {
    /**
     * Obtiene configuración del sitio con caching
     * 
     * @param string $config_name Nombre de la configuración
     * @return mixed
     */
    function get_site_config(string $config_name)
    {
        $cache_key = 'site_config_' . $config_name;
        
        // Intentar obtener del cache
        $cached_value = get_cached($cache_key);
        if ($cached_value !== null) {
            return $cached_value;
        }
        
        // Si no está en cache, obtener de base de datos
        $ci = &get_instance();
        $config_value = $ci->config->item($config_name);
        
        if (!$config_value) {
            $ci->load->model('Admin/SiteConfigModel');
            $config_obj = $ci->SiteConfigModel->where(['config_name' => $config_name]);
            $config_value = $config_obj ? $config_obj->first()->config_value : null;
        }
        
        // Cachear el resultado por 24 horas
        if ($config_value !== null) {
            set_cached($cache_key, $config_value, 86400);
        }
        
        return $config_value;
    }
}

if (!function_exists('get_fragment_cached')) {
    /**
     * Obtiene un fragmento con caching
     * 
     * @param string $fragment_name Nombre del fragmento
     * @return string
     */
    function get_fragment_cached(string $fragment_name): string
    {
        $cache_key = 'fragment_' . $fragment_name;
        
        // Intentar obtener del cache
        $cached_value = get_cached($cache_key);
        if ($cached_value !== null && $cached_value !== '') {
            return $cached_value;
        }
        
        // Si no está en cache, obtener de base de datos
        $ci = &get_instance();
        $ci->load->model('Admin/FragmentModel');
        $fragment = new FragmentModel();
        $result = $fragment->find_with(['name' => $fragment_name]);
        
        $fragment_content = '';
        if ($result) {
            $fragment_content = $result->description;
            // Cachear por 24 horas
            set_cached($cache_key, $fragment_content, 86400);
        }
        
        return $fragment_content;
    }
}

/* End of file cache_helper.php */
/* Location: ./application/helpers/cache_helper.php */
