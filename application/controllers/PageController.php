<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PageController extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Page');
        $config['enable_profiler'] = false;

    }

    public function index()
    {
        $data = $this->get_page_info(array('path' => $this->uri->uri_string(), 'status' => 1));
        if ($data == null) {
            $this->error404();
            return;
        }
        //Load local theme Controller
        echo $this->themeController->render($data);
    }

    public function home()
    {
        //Check if there are a page configured as home page
        $page_id = config("SITE_HOME_PAGE");
        if ($page_id) {
            $data = $this->get_page_info(array('page_id' => $page_id, 'status' => 1));
            if ($data == null) {
                $this->error404();
                return;
            }
            echo $this->themeController->render($data);
        } else {
            // Show default
            $data['title'] = config("SITE_TITLE") . " - Home";
            $data['layout'] = 'site';
            $data['template'] = 'home';
            echo $this->themeController->home($data, '');
        }
    }

    public function preview()
    {
        // Check user logged first
        if (!$this->session->userdata('logged_in')) {
            $uri = urlencode(uri_string());
            redirect('/');
        }

        $data = $this->get_page_info(array('page_id' => $this->input->get('page_id')));
        if ($data == null) {
            $this->error404();
            return;
        }

        //Load local theme Controller
        echo $this->themeController->render($data);

    }

    public function siteMap()
    {
        $data['pages'] = $this->Page->where(["status" => 1]);
        header("Content-Type: text/xml;charset=iso-8859-1");
        echo $this->blade->view("admin.xml.sitemap", $data);
    }

}
