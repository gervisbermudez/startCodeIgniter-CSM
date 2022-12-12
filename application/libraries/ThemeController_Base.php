<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ThemeController_Base
{
    public $blade;

    public function __construct()
    {
        $this->blade = new Blade();
    }

    /**
     * @return String
     */
    public function render($data)
    {
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        return $this->blade->view("site." . $data["template"], $data);
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
        return $this->blade->view("site." . $data["template"], $data);
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