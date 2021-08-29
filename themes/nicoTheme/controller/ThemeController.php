<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ThemeController
{
    private $blade;
    private $ci;

    public function __construct()
    {
        $this->blade = new Blade();
        $this->ci = &get_instance();
    }

    public function home($data)
    {
        $data['blogs'] = $this->ci->Page->where(['page_type_id' => 2, "status" => 1]);
        return $this->render($data, '');
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

}
