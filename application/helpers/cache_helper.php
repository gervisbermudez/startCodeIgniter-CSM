<?php
/**
 * Application cache (CI file driver).
 *
 * The driver is loaded lazily so helpers work on API, admin, and public
 * requests. Keys must stay [A-Za-z0-9:_-] for Cache_file.
 */

if (!function_exists('ensure_app_cache')) {
    /**
     * @return bool
     */
    function ensure_app_cache()
    {
        $ci = &get_instance();
        if (isset($ci->cache) && is_object($ci->cache)) {
            return true;
        }
        $ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
        return isset($ci->cache) && is_object($ci->cache);
    }
}

if (!function_exists('cache_id')) {
    /**
     * @param string $key
     * @return string
     */
    function cache_id($key)
    {
        return preg_replace('/[^A-Za-z0-9:_-]/', '_', (string) $key);
    }
}

if (!function_exists('get_cached')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_cached($key, $default = null)
    {
        if (!ensure_app_cache()) {
            return $default;
        }
        $ci = &get_instance();
        $cached = $ci->cache->get(cache_id($key));
        if ($cached !== false) {
            return $cached;
        }
        return $default;
    }
}

if (!function_exists('set_cached')) {
    /**
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    function set_cached($key, $value, $ttl = 3600)
    {
        if (!ensure_app_cache()) {
            return false;
        }
        $ci = &get_instance();
        return $ci->cache->save(cache_id($key), $value, (int) $ttl);
    }
}

if (!function_exists('delete_cached')) {
    /**
     * @param string $key
     * @return bool
     */
    function delete_cached($key)
    {
        if (!ensure_app_cache()) {
            return false;
        }
        $ci = &get_instance();
        return $ci->cache->delete(cache_id($key));
    }
}

if (!function_exists('delete_cache')) {
    /**
     * Alias used by admin/Cache.php
     *
     * @param string $key
     * @return bool
     */
    function delete_cache($key)
    {
        return delete_cached($key);
    }
}

if (!function_exists('flush_cache')) {
    /**
     * @return bool
     */
    function flush_cache()
    {
        if (!ensure_app_cache()) {
            return false;
        }
        $ci = &get_instance();
        return $ci->cache->clean();
    }
}

if (!function_exists('load_site_config_map')) {
    /**
     * Load site_config into CI config once per request. Cached as one map.
     *
     * @return array
     */
    function load_site_config_map()
    {
        $ci = &get_instance();
        if (!empty($ci->site_config_map_loaded) && is_array($ci->site_config_map)) {
            return $ci->site_config_map;
        }
        if (!empty($ci->site_config_map_loading)) {
            return isset($ci->site_config_map) && is_array($ci->site_config_map)
                ? $ci->site_config_map
                : array();
        }

        $ci->site_config_map_loading = true;
        $map = get_cached('site_config_map');
        if (!is_array($map)) {
            $ci->load->model('Admin/SiteConfigModel');
            $map = $ci->SiteConfigModel->get_map();
            if (!is_array($map)) {
                $map = array();
            }
            set_cached('site_config_map', $map, 86400);
        }

        foreach ($map as $name => $value) {
            $ci->config->set_item($name, $value);
        }

        $ci->site_config_map = $map;
        $ci->site_config_map_loaded = true;
        $ci->site_config_map_loading = false;

        return $map;
    }
}

if (!function_exists('app_cache_dir')) {
    /**
     * @return string
     */
    function app_cache_dir()
    {
        $ci = &get_instance();
        $path = $ci->config->item('cache_path');
        if (!is_string($path) || $path === '') {
            $path = APPPATH . 'cache/';
        }
        return rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }
}

if (!function_exists('delete_cached_prefix')) {
    /**
     * Unlink Cache_file entries whose id starts with $prefix.
     * Skips directories (sessions/) and dotfiles.
     *
     * @param string $prefix
     * @return void
     */
    function delete_cached_prefix($prefix)
    {
        $prefix = cache_id($prefix);
        if ($prefix === '') {
            return;
        }
        $dir = app_cache_dir();
        if (!is_dir($dir)) {
            return;
        }
        $files = glob($dir . $prefix . '*');
        if (!is_array($files)) {
            return;
        }
        foreach ($files as $file) {
            $base = basename($file);
            if ($base === '' || $base[0] === '.') {
                continue;
            }
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}

if (!function_exists('invalidate_public_html_cache')) {
    /**
     * Drop anonymous HTML and expanded page content. Fragments / config / home
     * id changes are not tied to a single page_id.
     *
     * @return void
     */
    function invalidate_public_html_cache()
    {
        delete_cached_prefix('html_');
        delete_cached_prefix('page_content_');
        delete_cached_prefix('page_html_id_');
    }
}

if (!function_exists('invalidate_site_config_cache')) {
    /**
     * @return void
     */
    function invalidate_site_config_cache()
    {
        delete_cached('site_config_map');
        $ci = &get_instance();
        $ci->site_config_map_loaded = false;
        $ci->site_config_map = array();
    }
}

if (!function_exists('public_html_cache_key')) {
    /**
     * @param string $kind
     * @param string $suffix
     * @return string
     */
    function public_html_cache_key($kind, $suffix)
    {
        return cache_id('html_' . $kind . '_' . md5((string) $suffix));
    }
}

if (!function_exists('can_cache_public_html')) {
    /**
     * Skip HTML cache for logged-in admins (contextual navbar) and query strings.
     *
     * @return bool
     */
    function can_cache_public_html()
    {
        $ci = &get_instance();
        if ($ci->session->userdata('logged_in')) {
            return false;
        }
        $qs = $ci->input->server('QUERY_STRING');
        if (is_string($qs) && $qs !== '') {
            return false;
        }
        return true;
    }
}

if (!function_exists('remember_page_html_alias')) {
    /**
     * Map page_id → HTML cache key so saves can bust without the path.
     *
     * @param int $page_id
     * @param string $html_key
     * @return void
     */
    function remember_page_html_alias($page_id, $html_key)
    {
        $page_id = (int) $page_id;
        if ($page_id < 1 || $html_key === '') {
            return;
        }
        set_cached('page_html_id_' . $page_id, $html_key, 3600);
    }
}

if (!function_exists('invalidate_page_cache')) {
    /**
     * @param object|int $page Page model or page_id
     * @return void
     */
    function invalidate_page_cache($page)
    {
        $page_id = 0;
        $path = '';
        if (is_object($page)) {
            $page_id = isset($page->page_id) ? (int) $page->page_id : 0;
            $path = isset($page->path) ? (string) $page->path : '';
        } else {
            $page_id = (int) $page;
        }

        if ($page_id > 0) {
            delete_cached('page_' . $page_id);
            delete_cached('page_full_' . $page_id);
            delete_cached('page_content_' . $page_id);
            delete_cached('page_data_' . $page_id);
            $alias = get_cached('page_html_id_' . $page_id);
            if (is_string($alias) && $alias !== '') {
                delete_cached($alias);
            }
            delete_cached('page_html_id_' . $page_id);
        }
        if ($path !== '') {
            delete_cached(public_html_cache_key('page', $path));
        }
        delete_cached(public_html_cache_key('home', 'index'));
        delete_cached(public_html_cache_key('blog', 'list'));
    }
}

if (!function_exists('invalidate_usergroup_permisions_cache')) {
    /**
     * @param int $usergroup_id
     * @return void
     */
    function invalidate_usergroup_permisions_cache($usergroup_id)
    {
        $usergroup_id = (int) $usergroup_id;
        if ($usergroup_id < 1) {
            return;
        }
        delete_cached('usergroup_permisions_' . $usergroup_id);
    }
}

if (!function_exists('get_site_config')) {
    /**
     * @param string $config_name
     * @return mixed
     */
    function get_site_config($config_name)
    {
        return config($config_name);
    }
}

if (!function_exists('get_fragment_cached')) {
    /**
     * @param string $fragment_name
     * @return string
     */
    function get_fragment_cached($fragment_name)
    {
        return fragment($fragment_name);
    }
}

/* End of file cache_helper.php */
/* Location: ./application/helpers/cache_helper.php */
