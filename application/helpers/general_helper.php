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
        $theme_path = $ci->config->item("THEME_PATH");
        if ($theme_path) {
            return FCPATH . 'themes\\' . $theme_path;
        }
        if(SITE_THEME){
            return FCPATH . 'themes\\' . SITE_THEME;
        }

        return null;
    }
}

if (!function_exists("config")) {
    function config($config_name)
    {
        $ci = &get_instance();
        $config = $ci->config->item($config_name);
        if ($config) {
            return $config;
        }
        return '';
    }
}

if (!function_exists('getThemePublicPath')) {
    function getThemePublicPath()
    {
        $ci = &get_instance();
        $config = new stdClass();
        $theme_path = isset($config->site_theme) ? $config->site_theme : SITE_THEME;
        if ($theme_path) {
            return 'themes/' . $theme_path . '/public/';
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
    function isSectionActive($path = '', $position = 2, $class = 'current')
    {
        if ($position === 'match') {
            if ($path == uri_string()) {
                return $class;
            }
            return '';
        }

        $ci = &get_instance();
        $url_array = $ci->uri->segment_array();

        if (count($url_array) == 0) {
            return '';
        }

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

if (!function_exists("time_ago")) {
    function time_ago($datetime, $full = false)
    {
        $now = new DateTime;

        $ago = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}

if (!function_exists('page_meta')) {
    function page_meta($metas)
    {
        $str = "";
        foreach ($metas as $meta) {
            $temp_str = "";
            foreach ($meta as $key => $value) {
                $temp_str .= $key . '="' . $value . '" ';
            }
            $str .= '<meta ' . $temp_str . '/>' . "\n";
        }
        return $str;
    }
}
