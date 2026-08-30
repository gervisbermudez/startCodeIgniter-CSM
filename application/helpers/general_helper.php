<?php
if (!function_exists('fnGetTitle')) {
    function fnGetTitle(string $strUrlSegment): string
    {
        $porciones = explode("/", $strUrlSegment);
        array_key_exists(1, $porciones) ? $title = ucwords($porciones[0] . " | " . $porciones[1]) : $title = ucwords($porciones[0]);
        array_key_exists(2, $porciones) ? $title = ucwords($porciones[0] . " | " . $porciones[1] . " - " . $porciones[2]) : false;
        return $title;
    }
}

if (!function_exists('url')) {
    function url(string $strUrlSegment): string
    {
        return base_url($strUrlSegment);
    }
}

function getTemplates(): array
{
    $ci = &get_instance();
    $ci->load->helper('directory');
    $layouts = directory_map(getThemePath() . '/views/site/layouts', 1);
    $templates = directory_map(getThemePath() . '/views/site/templates', 1);
    $pages = directory_map(getThemePath() . '/views/site', 1);

    function filter_files($strName)
    {
        return !(strpos($strName, "\\"));
    }

    function add_folder_path($strName)
    {
        return "templates." . $strName;
    }

    $layouts = array_filter($layouts, 'filter_files');
    $templates = array_filter($templates, 'filter_files');
    $templates = array_map('add_folder_path', $templates);
    $pages = array_filter($pages, 'filter_files');

    return [
        'layouts' => $layouts ? $layouts : [],
        'templates' => $templates ? array_merge($templates, $pages) : [],
    ];
}

if (!function_exists('getThemePath')) {
    function getThemePath(?string $theme = null): ?string
    {
        if ($theme) {
            return str_replace('\\', '/', FCPATH . 'themes' . '/' . $theme);
        }

        $theme_path = config("THEME_PATH");
        if ($theme_path) {
            return str_replace('\\', '/', FCPATH . 'themes' . '/' . $theme_path);
        }
        if (SITE_THEME) {
            return str_replace('\\', '/', FCPATH . 'themes' . '/' . SITE_THEME);
        }

        return null;
    }
}

function init_form(string $siteform_name): void
{
    $ci = &get_instance();
    $siteforms = $ci->session->userdata('siteforms');
    if (!is_array($siteforms)) {
        $siteforms = array();
    }
    if (!isset($siteforms[$siteform_name])) {
        $siteforms[$siteform_name] = array('submited' => 0);
        $ci->session->set_userdata('siteforms', $siteforms);
    }
}

function render_form(string $siteform_name): string
{
    $ci = &get_instance();
    $ci->load->model('Admin/SiteFormModel');
    $siteform = new SiteFormModel();
    $result = $siteform->find_with(['name' => $siteform_name]);
    if (!$result) {
        return '';
    }

    if (getThemePath()) {
        $ci->blade->changePath(getThemePath());
    }

    init_form($siteform_name);
    $ci->load->vars(array('siteform' => $siteform));

    return $ci->blade->view("site.templates.forms." . $siteform->template, ['siteform' => $siteform], true);
}

function fragment(string $fragment_name)
{
    // Obtener del cache si existe
    $cache_key = 'fragment_' . $fragment_name;
    $cached = get_cached($cache_key);
    if ($cached !== null && $cached !== '') {
        return $cached;
    }
    
    $ci = &get_instance();
    $ci->load->model('Admin/FragmentModel');
    $fragment = new FragmentModel();
    $result = $fragment->find_with(['name' => $fragment_name]);
    
    if (!$result) {
        return '';
    }

    $content = $result->description;
    // Cachear por 24 horas
    set_cached($cache_key, $content, 86400);
    
    return $content;
}

function set_notification(string $title, string $description, string $type = 'info', ?string $url = null): bool
{
    $ci = &get_instance();
    $ci->load->model('Admin/NotificationsModel');
    $notification = new NotificationsModel();
    $notification->title = $title;
    $notification->description = $description;
    $notification->type = $type;
    $notification->url = $url;
    $now = date("Y-m-d H:i:s");
    $notification->date_create = $now;
    $notification->date_update = $now;
    $notification->date_delete = $now;
    $notification->status = "1";
    if (function_exists('userdata') && userdata('user_id')) {
        $notification->user_id = userdata('user_id');
    }
    return $notification->save();
}

if (!function_exists("config")) {
    function config(string $config_name)
    {
        // Intentar obtener del cache primero
        $cached = get_cached('config_' . $config_name);
        if ($cached !== null) {
            return $cached;
        }
        
        $ci = &get_instance();
        $config = $ci->config->item($config_name);
        if ($config) {
            set_cached('config_' . $config_name, $config, 86400);
            return $config;
        }
        
        $ci->load->model('Admin/SiteConfigModel');
        $config = $ci->SiteConfigModel->where(['config_name' => $config_name]);
        $value = $config ? $config->first()->config_value : null;
        
        if ($value !== null) {
            set_cached('config_' . $config_name, $value, 86400);
        }
        
        return $value;
    }
}

if (!function_exists('getThemePublicPath')) {
    function getThemePublicPath(): string
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
    function isDir(string $dir): bool
    {
        return is_dir($dir['relative_path'] . $dir['name']);
    }
}

if (!function_exists('isSectionActive')) {
    function isSectionActive($path = '', $position = 2, $class = 'current')
    {
        $matched = false;

        if ($position === 'match') {
            $matched = (trim($path, '/') == trim(uri_string(), '/'));
        } else {
            $ci = &get_instance();
            $url_array = $ci->uri->segment_array();

            if (count($url_array) == 0) {
                return '';
            }

            if (count($url_array) < $position) {
                $position = 1;
            }

            $matched = ($path == $url_array[$position]);
        }

        if (!$matched) {
            return '';
        }

        if ($class === 'current') {
            return 'current active';
        }

        return $class;
    }
}

if (!function_exists('isNavItemActive')) {
    /**
     * Marks a sidenav leaf as current. Exact path, or prefix when the pattern ends with *.
     * Example: isNavItemActive(array('admin/pages', 'admin/pages/edit*'))
     */
    function isNavItemActive($paths, $class = 'current')
    {
        $uri = trim(uri_string(), '/');
        if ($uri === '') {
            return '';
        }
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            $path = trim($path, '/');
            if ($path === '') {
                continue;
            }
            $wildcard = substr($path, -1) === '*';
            if ($wildcard) {
                $prefix = rtrim(substr($path, 0, -1), '/');
                if ($prefix === '') {
                    continue;
                }
                if ($uri === $prefix || strpos($uri, $prefix . '/') === 0) {
                    return $class;
                }
            } elseif ($uri === $path) {
                return $class;
            }
        }
        return '';
    }
}

if (!function_exists('navCurrentAttr')) {
    function navCurrentAttr($paths)
    {
        return isNavItemActive($paths) ? 'aria-current="page"' : '';
    }
}

if (!function_exists('configNavCurrent')) {
    /**
     * Marks a Settings leaf in the main sidenav.
     * $page: index | data | logs. $section: ?section= value, or null for the whole page.
     */
    function configNavCurrent($page, $section = null, $class = 'current')
    {
        $ci = &get_instance();
        if ($ci->uri->segment(2) !== 'configuration') {
            return '';
        }

        $seg3 = $ci->uri->segment(3);
        if ($seg3 === false || $seg3 === null) {
            $seg3 = '';
        }

        $currentPage = 'index';
        if ($seg3 === 'data' || $seg3 === 'import' || $seg3 === 'export') {
            $currentPage = 'data';
        } elseif ($seg3 === 'logs' || $seg3 === 'logger' || $seg3 === 'apilogger' || $seg3 === 'usertrackinglogger') {
            $currentPage = 'logs';
        } elseif ($seg3 !== '' && $seg3 !== 'index') {
            $currentPage = $seg3;
        }

        $wantPage = ($page === 'home') ? 'index' : $page;
        if ($currentPage !== $wantPage) {
            return '';
        }

        if ($section === null) {
            return $class;
        }

        $querySection = $ci->input->get('section');
        if ($querySection === false || $querySection === null) {
            $querySection = '';
        }

        if ($querySection === 'analytics' || $querySection === 'pixel') {
            $querySection = 'integrations';
        }
        if ($wantPage === 'index' && $querySection === 'logger') {
            $querySection = 'system';
        }

        $defaults = array(
            'index' => 'home',
            'data' => 'backups',
        );
        if ($querySection === '' && isset($defaults[$wantPage])) {
            $querySection = $defaults[$wantPage];
        }

        return ($querySection === $section) ? $class : '';
    }
}

if (!function_exists('configNavCurrentAttr')) {
    function configNavCurrentAttr($page, $section = null)
    {
        return configNavCurrent($page, $section) ? 'aria-current="page"' : '';
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
        if (!is_array($usergroup_permisions)) {
            return false;
        }
        return in_array($permision, $usergroup_permisions, true);
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
    $ci->load->model('Admin/MenuModel');
    $menu = new MenuModel();
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
            if (file_exists(getThemePath() . '' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'site' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR . 'menu.blade.php')) {
                $blade->changePath(getThemePath());
            } else {
                $blade->changePath(APPPATH);
            }
        }
        $rendered_menu = $blade->view("site.templates.menu." . $menu->template, $data, true);

        return $rendered_menu;
    }
}

function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }

    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function system_logger($type, $type_id, $token, $comment = '')
{
    $ci = &get_instance();
    // Cachear el estado del logger para evitar múltiples consultas a la DB en el mismo request
    static $logger_active = null;

    if ($logger_active === null) {
        $ci->load->model('Admin/SiteConfigModel');
        $result = $ci->SiteConfigModel->find_with(["config_name" => 'SYSTEM_LOGGER']);
        $logger_active = ($result && $result->config_value == '1');
    }

    if ($logger_active) {
        $ci->load->model('Admin/LoggerModel');
        $data = [
            'user_id' => userdata('user_id'),
            'type_id' => $type_id,
            'type' => $type,
            'token' => $token,
            'comment' => $comment,
            'status' => 1,
            'date_create' => date("Y-m-d H:i:s")
        ];
        return $ci->db->insert('logger', $data);
    }
    return false;
}

/**
 * Class casting
 * @see https: //stackoverflow.com/questions/3243900/convert-cast-an-stdclass-object-to-another-class
 * @param string|object $destination
 * @param object $sourceObject
 * @return object
 */
function cast($destination, $sourceObject)
{
    if (is_string($destination)) {
        if (!class_exists($destination)) {
            $ci = &get_instance();
            $ci->load->model('Admin/' . $destination);
        }
        $destination = new $destination();
    }
    $sourceReflection = new ReflectionObject($sourceObject);
    $destinationReflection = new ReflectionObject($destination);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);
        if ($destinationReflection->hasProperty($name)) {
            $propDest = $destinationReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($destination, $value);
        } else {
            $destination->$name = $value;
        }
    }
    return $destination;
}
