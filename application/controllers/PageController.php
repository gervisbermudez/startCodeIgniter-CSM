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

    public function blog_list()
    {
        $data['title'] = config("SITE_TITLE") . " - Blog";
        $data['blogs'] = $this->Page->where(['page_type_id' => 2, "status" => 1]);
        $data['layout'] = 'site';
        $data['template'] = 'blogList';
        echo $this->themeController->blog_list($data);
    }

    public function get_blog()
    {
        $data = $this->get_page_info(array('path' => $this->uri->uri_string(), 'status' => 1));
        if ($data == null) {
            $this->error404();
            return;
        }

        //Load local theme Controller
        echo $this->themeController->blog_post($data);
    }

    public function formsubmit()
    {
        $siteform_id = $this->input->post('form_reference');
        $this->load->model('Admin/SiteForm');
        $siteform = new SiteForm();
        $result = $siteform->where(['siteform_id' => $siteform_id]);
        if ($result) {
            $siteforms = $this->session->userdata('siteforms');
            if ($siteforms && isset($siteforms[$result->first()->name])) {
                $submited_form = $siteforms[$result->first()->name];
                $date = new DateTime();
                if (isset($submited_form['timestamp'])) {
                    $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $submited_form['timestamp']);
                    $interval = $date->diff($datetime2);
                    if ($interval->format('%I') >= 3) {
                        $this->process_form_submit();

                    }
                } else {
                    $this->process_form_submit();
                }
                $submited = [$result->first()->name => ['submited' => $submited_form['submited'] + 1, "timestamp" => $date->format('Y-m-d H:i:s')]];
                $this->session->set_userdata('siteforms', $submited);
                $data['title'] = config("SITE_TITLE") . " - Submited Form";
                $data['layout'] = 'site';
                $data['template'] = 'templates.default';
                $data['page'] = (Object) ["title" => lang('form_submited_title'), "subtitle" => "", "content" => lang("form_submited_message")];
                echo $this->themeController->render($data, '');
            }
        }else{
            redirect("/");
        }
    }

    private function process_form_submit()
    {
        $this->load->model('Admin/Siteform_submit');
        $siteform_submit = new Siteform_submit();
        $siteform_submit->siteform_id = $this->input->post('form_reference');
        $siteform_submit->user_tracking_id = userdata('user_tracking_id');
        unset($_POST['form_reference']);
        $siteform_submit->status = 1;
        $siteform_submit->siteform_submit_data = $_POST;
        $save_result = $siteform_submit->save();
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

    public function blogFeed()
    {
        $this->load->helper('xml');
        $this->load->helper('text');
        $data['feed_name'] = config("SITE_TITLE");
        $data['encoding'] = 'UTF-8';
        $data['feed_url'] = base_url('feed');
        $data['page_description'] = config("SITE_DESCRIPTION");
        $data['page_language'] = 'en-en';
        $data['creator_email'] = config("SITE_ADMIN_EMAIL");
        $data['site_language'] = config("SITE_LANGUAGE");
        $data['posts'] = $this->Page->where(['page_type_id' => 2, "status" => 1]);
        header("Content-Type: application/rss+xml");
        echo $this->blade->view("admin.xml.rss", $data);
    }

}
