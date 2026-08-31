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

if (!function_exists('getTemplates')) {
    function getTemplates(): array
    {
        $ci = &get_instance();
        $ci->load->helper('directory');
        $theme_path = getThemePath();
        $layouts = $theme_path ? directory_map($theme_path . '/views/site/layouts', 1) : false;
        $templates = $theme_path ? directory_map($theme_path . '/views/site/templates', 1) : false;
        $pages = $theme_path ? directory_map($theme_path . '/views/site', 1) : false;

        if (!is_array($layouts)) {
            $layouts = array();
        }
        if (!is_array($templates)) {
            $templates = array();
        }
        if (!is_array($pages)) {
            $pages = array();
        }

        $filter_files = function ($strName) {
            return !(strpos($strName, "\\"));
        };

        $add_folder_path = function ($strName) {
            return "templates." . $strName;
        };

        $layouts = array_filter($layouts, $filter_files);
        $templates = array_filter($templates, $filter_files);
        $templates = array_map($add_folder_path, $templates);
        $pages = array_filter($pages, $filter_files);

        return [
            'layouts' => $layouts ? $layouts : [],
            'templates' => $templates ? array_merge($templates, $pages) : [],
        ];
    }
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
    if (!$siteforms && !isset($siteforms[$siteform_name])) {
        $ci->session->set_userdata('siteforms', [$siteform_name => ['submited' => 0]]);
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
    $ci->rendered_siteform = $siteform;

    return $ci->blade->view("site.templates.forms." . $siteform->template, ['siteform' => $siteform], true);
}

/**
 * Expand {{ helper(args) }} and {!! helper(args) !!} in CMS HTML (pages / fragments).
 * Theme override for collections: {theme}/views/site/templates/collections/{template}.blade.php
 */
function expand_helper_snippets($html)
{
    if (!is_string($html) || $html === '') {
        return $html;
    }
    $patterns = array(
        '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\((.*?)\)\s*\}\}/',
        '/\{!!\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\((.*?)\)\s*!!\}/',
    );
    $passes = 0;
    while ($passes < 8) {
        $passes++;
        $replaced = false;
        foreach ($patterns as $pattern) {
            $html = preg_replace_callback($pattern, function ($m) use (&$replaced) {
                $fn = $m[1];
                if (!is_callable($fn)) {
                    return $m[0];
                }
                $args = array();
                $raw = trim($m[2]);
                if ($raw !== '') {
                    $parts = explode(',', $raw);
                    foreach ($parts as $part) {
                        $args[] = trim($part, " \t\n\r\"'");
                    }
                }
                $replaced = true;
                $result = call_user_func_array($fn, $args);
                return is_string($result) ? $result : '';
            }, $html);
        }
        if (!$replaced) {
            break;
        }
    }
    return $html;
}

function list_collection_templates()
{
    $names = array();
    $dirs = array(APPPATH . 'views/site/templates/collections');
    $theme = getThemePath();
    if ($theme) {
        $dirs[] = $theme . '/views/site/templates/collections';
    }
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            continue;
        }
        $files = @scandir($dir);
        if (!is_array($files)) {
            continue;
        }
        foreach ($files as $file) {
            if (substr($file, -10) === '.blade.php') {
                $names[] = substr($file, 0, -10);
            }
        }
    }
    $names = array_values(array_unique($names));
    sort($names);
    return $names;
}

function resolve_collection_template($template)
{
    $template = preg_replace('/[^a-z0-9_\-]/i', '', (string) $template);
    if ($template === '') {
        $template = 'default';
    }
    $rel = '/views/site/templates/collections/' . $template . '.blade.php';
    $theme = getThemePath();
    if ($theme && file_exists($theme . $rel)) {
        return array('path' => $theme, 'template' => $template);
    }
    $fallback = APPPATH . 'views/site/templates/collections/' . $template . '.blade.php';
    if (file_exists($fallback)) {
        return array('path' => APPPATH, 'template' => $template);
    }
    if ($theme && file_exists($theme . '/views/site/templates/collections/default.blade.php')) {
        return array('path' => $theme, 'template' => 'default');
    }
    return array('path' => APPPATH, 'template' => 'default');
}

/**
 * First matching field on a normalized collection item (image|imagen, url|link, …).
 *
 * @param object $item
 * @param array|string $keys
 * @return mixed|null
 */
function collection_item_field($item, $keys)
{
    if (!is_object($item) || empty($item->fields) || !is_array($item->fields)) {
        return null;
    }
    if (!is_array($keys)) {
        $keys = array($keys);
    }
    foreach ($keys as $key) {
        if (isset($item->fields[$key]) && $item->fields[$key] !== '' && $item->fields[$key] !== null) {
            return $item->fields[$key];
        }
    }
    return null;
}

/**
 * @param object $item
 * @return object|null
 */
function collection_item_image($item)
{
    $val = collection_item_field($item, array('image', 'imagen', 'photo', 'picture'));
    if (is_object($val) && !empty($val->url)) {
        return $val;
    }
    return null;
}

/**
 * @param object $item
 * @return string
 */
function collection_item_url($item)
{
    $val = collection_item_field($item, array('url', 'link', 'href'));
    if ($val !== null && !is_object($val) && !is_array($val) && $val !== '') {
        return $val;
    }
    return '';
}

/**
 * HTML of a published collection (type status = 1, items status = 1).
 * Empty string if the slug is missing or inactive (not a 404).
 * Theme override: {theme}/views/site/templates/collections/{template}.blade.php
 *
 * @param string $slug
 * @param array $options limit, featured, template
 * @return string
 */
function get_collection($slug, $options = array())
{
    if (!is_array($options)) {
        $options = array();
    }
    $items = get_collection_items($slug, $options);
    $ci = &get_instance();
    $ci->load->model('Admin/CustomModelModel');
    $slug = trim((string) $slug, " \t\n\r\"'");
    $type = new CustomModelModel();
    if (!$type->find_with(array('slug' => $slug, 'status' => 1))) {
        return '';
    }
    $templateName = !empty($options['template']) ? $options['template'] : $type->template;
    $collection = (object) array(
        'name' => $type->form_name,
        'slug' => $type->slug ? $type->slug : $slug,
        'description' => $type->form_description,
        'template' => $templateName ? $templateName : 'default',
        'items' => $items,
    );
    $resolved = resolve_collection_template($collection->template);
    $collection->template = $resolved['template'];
    $originalViews = isset($ci->blade->views) ? $ci->blade->views : (APPPATH . 'views');
    $ci->blade->changePath($resolved['path']);
    $html = $ci->blade->view('site.templates.collections.' . $resolved['template'], array('collection' => $collection), true);
    $restoreBase = str_replace('/views', '', $originalViews);
    if ($restoreBase === '' || $restoreBase === $originalViews) {
        $restoreBase = APPPATH;
    }
    $ci->blade->changePath($restoreBase);
    return is_string($html) ? $html : '';
}

/**
 * Normalized published items for a collection slug (no HTML).
 *
 * @param string $slug
 * @param array $options limit, featured
 * @return array
 */
function get_collection_items($slug, $options = array())
{
    if (!is_array($options)) {
        $options = array();
    }
    $ci = &get_instance();
    $ci->load->model('Admin/CustomModelModel');
    $ci->load->model('Admin/CustomModelContentModel');
    $slug = trim((string) $slug, " \t\n\r\"'");
    if ($slug === '') {
        return array();
    }
    $type = new CustomModelModel();
    if (!$type->find_with(array('slug' => $slug, 'status' => 1))) {
        return array();
    }
    $contentModel = new CustomModelContentModel();
    return $contentModel->get_normalized_items($type, $options);
}

function fragment(string $fragment_name)
{
    // Cache raw markup only. Expand helpers on every read so get_collection()
    // (and render_form(), etc.) stay fresh after item/type saves.
    $cache_key = 'fragment_raw_' . $fragment_name;
    $cached = get_cached($cache_key);
    if ($cached !== null && $cached !== '') {
        return expand_helper_snippets($cached);
    }

    $ci = &get_instance();
    $ci->load->model('Admin/FragmentModel');
    $fragment = new FragmentModel();
    $result = $fragment->find_with(['name' => $fragment_name]);

    if (!$result) {
        return '';
    }

    $content = $result->description;
    set_cached($cache_key, $content, 86400);

    return expand_helper_snippets($content);
}

function set_notification($title, $description, $type = 'info', $url = null, $user_ids = null)
{
    $ci = &get_instance();
    $ci->load->model('Admin/NotificationsModel');
    $ci->load->model('Admin/UserModel');

    if ($user_ids === null) {
        $user_ids = array();
        $users = (new UserModel())->all();
        if ($users) {
            foreach ($users as $user) {
                $user_ids[] = $user->user_id;
            }
        }
    } else {
        $user_ids = is_array($user_ids) ? $user_ids : array($user_ids);
    }

    if (empty($user_ids)) {
        return true;
    }

    if (is_string($url) && $url !== '') {
        $url = ltrim($url, '/');
    }

    $ok = true;
    foreach ($user_ids as $uid) {
        $uid = (int) $uid;
        if ($uid < 1) {
            continue;
        }
        $notification = new NotificationsModel();
        $notification->title = $title;
        $notification->description = $description;
        $notification->type = $type;
        $notification->url = $url;
        $notification->user_id = $uid;
        $notification->date_create = date('Y-m-d H:i:s');
        $notification->date_update = $notification->date_create;
        $notification->status = '1';
        if (!$notification->save()) {
            $ok = false;
        }
    }
    return $ok;
}

if (!function_exists('siteform_should_notify')) {
    function siteform_should_notify($siteform)
    {
        if (!$siteform) {
            return true;
        }
        $props = isset($siteform->properties) ? $siteform->properties : null;
        if (is_string($props)) {
            $props = json_decode($props);
        }
        if (is_object($props) && isset($props->notify) && $props->notify === false) {
            return false;
        }
        if (is_array($props) && array_key_exists('notify', $props) && $props['notify'] === false) {
            return false;
        }
        return true;
    }
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

if (!function_exists('theme_site_view_root')) {
    /**
     * Parent of /views for a site template: theme if the file exists, else APPPATH.
     *
     * @param string $template e.g. eventsList or templates.event_card
     * @return string
     */
    function theme_site_view_root($template)
    {
        $relative = 'views/site/' . str_replace('.', '/', $template) . '.blade.php';
        $theme = getThemePath();
        if ($theme && file_exists($theme . '/' . $relative)) {
            return $theme;
        }
        return rtrim(APPPATH, '/\\');
    }
}

if (!function_exists('render_event')) {
    /**
     * Published+visible event card HTML, or empty string.
     *
     * @param string $slug
     * @return string
     */
    function render_event($slug)
    {
        $ci = &get_instance();
        $ci->load->model('Admin/EventModel');
        $event = new EventModel();
        if (!$event->find_by_slug($slug)) {
            return '';
        }
        $template = 'templates.event_card';
        $blade = new Blade();
        $blade->changePath(theme_site_view_root($template));
        return $blade->view('site.' . $template, array('event' => $event), true);
    }
}
