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
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }

        echo $this->blade->view("site.templates." . $data["template"], $data);

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
            if (getThemePath()) {
                $this->blade->changePath(getThemePath());
            }

            echo $this->blade->view("site.templates." . $data["template"], $data);
        } else {
            // Show default
            $data['title'] = config("SITE_TITLE") . " - Home";
            if (getThemePath()) {
                $this->blade->changePath(getThemePath());
            }
            echo $this->blade->view("site.home", $data);
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
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }

        echo $this->blade->view("site.templates." . $data["template"], $data);

    }

    public function siteMap()
    {
        $data['pages'] = $this->Page->where(["status" => 1]);
        header("Content-Type: text/xml;charset=iso-8859-1");
        echo $this->blade->view("admin.xml.sitemap", $data);
    }

    private function get_page_info($where)
    {
        $pageInfo = new Page();
        $data['result'] = $pageInfo->find_with($where);
        if (!$data['result']) {
            //Not found Page
            return null;
        }
        //Is the page published?
        $date_now = new DateTime();
        $data['pagePublishTime'] = DateTime::createFromFormat('Y-m-d H:i:s', $pageInfo->date_publish);

        if ($date_now < $data['pagePublishTime']) {
            return null;
        }

        $data['page'] = $pageInfo;
        $data['meta'] = $this->getPageMetas($pageInfo);
        $data['title'] = $pageInfo->page_data["title"] ? config("SITE_TITLE") . " - " . $pageInfo->page_data["title"] : config("SITE_TITLE") . " - " . $pageInfo->title;
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        $data['headers_includes'] = isset($pageInfo->page_data["headers_includes"]) ? $pageInfo->page_data["headers_includes"] : "";
        $data['footer_includes'] = isset($pageInfo->page_data["footer_includes"]) ? $pageInfo->page_data["footer_includes"] : "";
        $data['template'] = $pageInfo->template == 'default' ? 'template' : $pageInfo->template;
        $this->load->model('Admin/Menu');
        $menu = new Menu();
        $menu->find_with(['menu_id' => 1]);
        $data["menu"] = $menu;
        return $data;
    }

}
