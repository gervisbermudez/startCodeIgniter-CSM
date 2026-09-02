<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Legacy alias. Routes use ConfigurationController.
 */
require_once APPPATH . 'controllers/admin/ConfigurationController.php';

class Configuration extends ConfigurationController
{
}
