<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use eftec\bladeone\BladeOne;

class Blade
{

    public $views = APPPATH . 'views';
    public $cache = APPPATH . 'cache';
    private $BladeOne = null;

    public function __construct()
    {
        $this->BladeOne = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
    }

    /**
     * @param $view_name String template named .balde.php
     * @param $params Object params to render into the view
     */
    public function view($view_name, $params)
    {
        $ci = &get_instance();
        $params['ci'] = $ci;
        // expose common CI objects to the BladeOne instance so compiled templates
        // can access them via $this->session, $this->config, etc.
        $this->BladeOne->session = isset($ci->session) ? $ci->session : null;
        $this->BladeOne->config = isset($ci->config) ? $ci->config : null;
        $this->BladeOne->input = isset($ci->input) ? $ci->input : null;
        return $this->BladeOne->run($view_name, $params);
    }

    /**
     * @param $path string new path to set
     */
    public function changePath($path)
    {
        $this->views = $path . '/views';
        $this->cache = $path . '/cache';
        $this->BladeOne = new BladeOne($this->views, $this->cache, BladeOne::MODE_AUTO);
    }

}

/* End of file Someclass.php */
