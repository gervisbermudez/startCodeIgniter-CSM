<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ThemeController
{
    private $blade;

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
        return $this->blade->view("site.templates." . $data["template"], $data);
    }

}
