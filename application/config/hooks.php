<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/**
 * Auto Backup Hook
 * Runs after controller initialization to check if automatic backup is needed
 * This provides a "pseudo-cron" solution for servers without cron access
 */
$hook['post_controller_constructor'] = array(
    'class'    => 'Auto_backup_hook',
    'function' => 'check_and_backup',
    'filename' => 'Auto_backup_hook.php',
    'filepath' => 'hooks'
);
