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
        return $this->BladeOne->run($view_name, $params);
    }

    /**
     * @param $path string new path to set
     */
    public function changePath($path)
    {
        $this->views = $path . '\\views';
        $this->BladeOne = new BladeOne($path . '\\views', $this->cache, BladeOne::MODE_AUTO);
    }

}

/* End of file Someclass.php */
