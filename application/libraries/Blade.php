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
     * @param $view_name String template named .blade.php
     * @param $params Array params to render into the view
     * @param $return_output Boolean if true, returns the output; if false, echoes it
     */
    public function view($view_name, $params = [], $return_output = true)
    {
        $ci = &get_instance();
        $params['ci'] = $ci;
        // expose common CI objects to the BladeOne instance so compiled templates
        // can access them via $this->session, $this->config, etc.
        $this->BladeOne->session = isset($ci->session) ? $ci->session : null;
        $this->BladeOne->config = isset($ci->config) ? $ci->config : null;
        $this->BladeOne->input = isset($ci->input) ? $ci->input : null;
        
        $output = $this->BladeOne->run($view_name, $params);
        
        if ($return_output) {
            // Return the rendered output
            return $output;
        } else {
            // Display the output directly
            echo $output;
        }
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
