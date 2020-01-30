<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Welcome extends CI_Controller
{

    public function index()
    {
        $data['title'] = "Modern Business - Start Bootstrap Template";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.home", $data);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
