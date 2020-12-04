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
        $theme_path = str_replace('\\', '/', $theme_path);
        if ($theme_path) {
            return FCPATH . 'themes/' . $theme_path;
        }
        if (SITE_THEME) {
            return FCPATH . 'themes/' . SITE_THEME;
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
        $config = $ci->Site_config->where(['config_name' => $config_name]);
        return $config ? $config->first()->config_value : null;
    }
}

if (!function_exists('getThemePublicPath')) {
    function getThemePublicPath()
    {
        $ci = &get_instance();
        $config = new stdClass();
        $theme_path = $ci->config->item("THEME_PATH");
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

if (!function_exists('has_permisions')) {
    function has_permisions($permision)
    {
        $ci = &get_instance();
        $usergroup_permisions = $ci->session->userdata('usergroup_permisions');
        return in_array($permision, $usergroup_permisions);
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

// Function to remove folders and files
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rrmdir("$dir/$file");
            }
        }

        rmdir($dir);
    } else if (file_exists($dir)) {
        unlink($dir);
    }
}

// Function to Copy folders and files
function rcopy($src, $dst)
{
    if (file_exists($dst)) {
        rrmdir($dst);
    }

    if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rcopy("$src/$file", "$dst/$file");
            }
        }
    } else if (file_exists($src)) {
        copy($src, $dst);
    }
}

function recurse_copy($src, $dst, $ignorefiles = [])
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (!in_array($file, $ignorefiles)) {

            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file, $ignorefiles);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function get_menu($name)
{
    $ci = &get_instance();
    $ci->load->model('Admin/Menu');
    $menu = new Menu();
    $result = $menu->find_with(['name' => $name]);
    return $result ? $menu->as_data() : null;
}

function render_menu($name)
{
    $menu = get_menu($name);
    if ($menu) {
        $data["menu"] = $menu;

        $ci = &get_instance();
        $blade = new Blade();

        if (getThemePath()) {
            if (file_exists(getThemePath() . '\\views\\site\\templates\\menu\\menu.blade.php')) {
                $blade->changePath(getThemePath());
            } else {
                $blade->changePath(APPPATH);
            }
        }
        $rendered_menu = $blade->view("site.templates.menu." . $menu->template, $data);

        return $rendered_menu;
    }
}

function system_logger($type, $type_id, $token, $comment = '')
{

    $ci = &get_instance();
    $ci->load->model('Admin/Site_config');
    $Site_config = new Site_config();
    $result = $Site_config->find_with(["config_name" => 'SYSTEM_LOGGER']);
    $logger_active = false;
    if ($result && $Site_config->config_value) {
        $logger_active = true;
    }
    if ($logger_active) {
        $ci->load->model('Admin/Logger');
        $Logger = new Logger();
        $Logger->logger_id = null;
        $Logger->user_id = userdata('user_id');
        $Logger->type_id = $type_id;
        $Logger->type = $type;
        $Logger->token = $token;
        $Logger->comment = $comment;
        $Logger->status = 1;
        $Logger->date_create = date("Y-m-d H:i:s");
        return $Logger->save();
    }
    return false;
}
