<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper para simplificar directivas de permisos en vistas
 * Reduce código repetitivo y mejora legibilidad
 */

/**
 * Genera atributos condicionales basados en permisos
 * Uso: <a <?= permission_attr('UPDATE_PAGE', 'href', base_url('admin/pages/edit/1')) ?>>
 * 
 * @param string $permission Nombre del permiso
 * @param string $attribute Nombre del atributo HTML
 * @param string $value Valor del atributo
 * @return string Atributo HTML si tiene permiso, string vacío si no
 */
function permission_attr($permission, $attribute, $value)
{
    if (has_permisions($permission)) {
        return $attribute . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
    }
    return '';
}

/**
 * Genera clases CSS condicionales basadas en permisos
 * Uso: <div class="btn <?= permission_class('UPDATE_PAGE', 'active', 'disabled') ?>">
 * 
 * @param string $permission Nombre del permiso
 * @param string $hasClass Clase si tiene permiso
 * @param string $noClass Clase si no tiene permiso (opcional)
 * @return string Clase CSS correspondiente
 */
function permission_class($permission, $hasClass, $noClass = '')
{
    return has_permisions($permission) ? $hasClass : $noClass;
}

/**
 * Renderiza contenido solo si tiene permisos
 * Uso: <?= permission_render('UPDATE_PAGE', '<a href="#">Editar</a>') ?>
 * 
 * @param string $permission Nombre del permiso
 * @param string $content Contenido HTML a renderizar
 * @return string Contenido si tiene permiso, string vacío si no
 */
function permission_render($permission, $content)
{
    return has_permisions($permission) ? $content : '';
}

/**
 * Genera un array de opciones de menú filtrado por permisos
 * Uso en Vue: options="<?= permission_menu_options($options) ?>"
 * 
 * @param array $options Array asociativo ['label' => 'Editar', 'permission' => 'UPDATE_PAGE', 'href' => '...']
 * @return string JSON con opciones permitidas
 */
function permission_menu_options($options)
{
    $filtered = array_filter($options, function($option) {
        return !isset($option['permission']) || has_permisions($option['permission']);
    });
    
    return json_encode(array_values($filtered));
}

/**
 * Verifica múltiples permisos (AND logic)
 * Uso: <?php if (has_any_permissions(['UPDATE_PAGE', 'DELETE_PAGE'])): ?>
 * 
 * @param array $permissions Array de nombres de permisos
 * @return bool True si tiene alguno de los permisos
 */
function has_any_permissions($permissions)
{
    foreach ($permissions as $permission) {
        if (has_permisions($permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Verifica que tenga todos los permisos (AND logic)
 * 
 * @param array $permissions Array de nombres de permisos
 * @return bool True si tiene todos los permisos
 */
function has_all_permissions($permissions)
{
    foreach ($permissions as $permission) {
        if (!has_permisions($permission)) {
            return false;
        }
    }
    return true;
}
