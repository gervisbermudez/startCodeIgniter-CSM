<?php
if (!function_exists('fnGetTitle')) {
    function fnGetTitle($strUrlSegment)
    {
        $porciones = explode("/", $strUrlSegment);
        array_key_exists(1, $porciones) ? $title = ucwords($porciones[0] . " | " . $porciones[1]) : $title = ucwords($porciones[0]);
        array_key_exists(2, $porciones) ? $title = ucwords($porciones[0] . " | " . $porciones[1] . " - " . $porciones[2]) : false;
        return $title;
    }
}

if (!function_exists('getThemePath')) {
    function getThemePath()
    {
        $ci = &get_instance();
        $config = $ci->Site_config->load_config();

        if (isset($config->site_theme)) {
            defined('THEME_PATH') or define('THEME_PATH', FCPATH . 'themes\\' . $config->site_theme) . '\\';
            return THEME_PATH;
        }
        return '';
    }
}

if (!function_exists('getThemePublicPath')) {
    function getThemePublicPath()
    {
        $ci = &get_instance();
        $config = $ci->Site_config->load_config();

        if ($config->site_theme) {
            return 'themes/' . $config->site_theme . '/public/';
        }

        return '';

    }
}

if (!function_exists('isDir')) {
    function isDir($dir)
    {
        return is_dir($dir['relative_path'] . $dir['name']);
    }
}

if (!function_exists('isSectionActive')) {
    function isSectionActive($path = '', $position = 2, $class = 'active')
    {
        $ci = &get_instance();
        $url_array = $ci->uri->segment_array();
        if (count($url_array) < $position) {
            $position = 1;
        }
        if ($path == $url_array[$position]) {
            return $class;
        }

        return '';
    }
}

if (!function_exists('userdata')) {
    function userdata($index)
    {
        $ci = &get_instance();
        return $ci->session->userdata($index);
    }
}

if (!function_exists('slugify')) {
    function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}

if (!function_exists('userdata')) {
    function userdata($text)
    {
        $ci = &get_instance();
        return $ci->session->userdata($text);
    }

}

if (!function_exists('script')) {
    function script($url)
    {
        return '<script src="' . base_url($url) . '"></script>';
    }

}
