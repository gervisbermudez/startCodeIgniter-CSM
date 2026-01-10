<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ThemeController_Base
{
    public $blade;
    protected $ci;

    public function __construct()
    {
        $this->blade = new Blade();
        $this->ci =& get_instance();
    }

    /**
     * Inject admin navbar if user is logged in
     * @param string $content HTML content
     * @return string Modified HTML content with admin navbar
     */
    protected function injectAdminNavbar($content)
    {
        // Check if user is logged in
        if ($this->ci->session->userdata('logged_in')) {
            // Load the admin navbar view
            $adminNavbar = $this->ci->blade->view('shared.admin_navbar', [], true);
            
            // Inject MaterializeCSS and Material Icons if not already present
            $headInjection = '';
            if (strpos($content, 'materialize.min.css') === false) {
                $headInjection .= '<link href="' . base_url('public/css/materialize.min.css?v=' . ADMIN_VERSION) . '" rel="stylesheet">' . "\n";
            }
            if (strpos($content, 'Material+Icons') === false) {
                $headInjection .= '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">' . "\n";
            }
            
            // Inject CSS and JS in head if needed
            if ($headInjection) {
                $content = str_replace('</head>', $headInjection . '</head>', $content);
            }
            
            // Inject admin navbar after <body> tag
            $content = preg_replace('/(<body[^>]*>)/i', '$1' . "\n" . $adminNavbar, $content);
            
            // Inject MaterializeCSS JS before </body> if not already present
            if (strpos($content, 'materialize.min.js') === false) {
                $jsInjection = '<script src="' . base_url('public/js/materialize.min.js?v=' . ADMIN_VERSION) . '"></script>' . "\n";
                $content = str_replace('</body>', $jsInjection . '</body>', $content);
            }
        }
        
        return $content;
    }

    /**
     * @return String
     */
    public function render($data)
    {
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        $content = $this->blade->view("site." . $data["template"], $data);
        return $this->injectAdminNavbar($content);
    }

    /**
     * Custom error 404
     * @return String
     */
    public function error404($data)
    {
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        $content = $this->blade->view("site." . $data["template"], $data);
        return $this->injectAdminNavbar($content);
    }

    /**
     * @return String
     */
    public function home($data)
    {
        return $this->render($data);
    }

    public function blog_list($data)
    {
        return $this->render($data);
    }

    public function blog_post($data)
    {
        return $this->render($data);
    }

}