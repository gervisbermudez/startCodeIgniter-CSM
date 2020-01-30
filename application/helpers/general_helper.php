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
        $config = $ci->Config_model->load_config();

        if ($config->site_theme) {
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
        $config = $ci->Config_model->load_config();

        if ($config->site_theme) {
            return 'themes/' . $config->site_theme . '/public/';
        }

        return '';

    }
}

function isDir($dir)
{
    return is_dir($dir['relative_path'] . $dir['name']);
}
