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
        if ($ci->config->item('theme_active')) {
            defined('THEME_PATH') or define('THEME_PATH', FCPATH . 'themes\\' . $ci->config->item('theme_active')) . '\\';
            return THEME_PATH;
        }
        return '';
    }
}

if (!function_exists('getThemePublicPath')) {
    function getThemePublicPath()
    {
        $ci = &get_instance();
        if ($ci->config->item('theme_active')) {
            return 'themes/' . $ci->config->item('theme_active') . '/public/';
        }

        return '';

    }
}
