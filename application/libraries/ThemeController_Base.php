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
     * @param array $data Data passed to the view (contains page, blog, siteform, etc)
     * @return string Modified HTML content with admin navbar
     */
    protected function injectAdminNavbar($content, $data = [])
    {
        // Check if user is logged in
        if ($this->ci->session->userdata('logged_in')) {
            // Save current blade path
            $originalViewPath = $this->ci->blade->views;
            
            // Ensure blade is pointing to application views
            if ($this->ci->blade->views !== APPPATH . 'views') {
                $this->ci->blade->changePath(APPPATH);
            }
            
            // Load the admin navbar view with the page/blog/form data
            $adminNavbar = $this->ci->blade->view('shared.admin_navbar', $data, true);
            
            // Restore original path if it was different
            if ($originalViewPath !== APPPATH . 'views') {
                // Extract base path from views path
                $basePath = str_replace('/views', '', $originalViewPath);
                $this->ci->blade->changePath($basePath);
            }
            
            // Inject Material Icons only (no MaterializeCSS to avoid conflicts)
            $headInjection = '';
            if (strpos($content, 'Material+Icons') === false) {
                $headInjection .= '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">' . "\n";
            }
            
            // Inject Material Icons in head if needed
            if ($headInjection) {
                $content = str_replace('</head>', $headInjection . '</head>', $content);
            }
            
            // Inject admin navbar after <body> tag
            $content = preg_replace('/(<body[^>]*>)/i', '$1' . "\n" . $adminNavbar, $content);
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
        return $this->injectAdminNavbar($content, $data);
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