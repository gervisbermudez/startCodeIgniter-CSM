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
        $page_id = config("SITE_HOME_PAGE_ID");
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
            $data['meta'] = $this->getPageMetas([]);

            echo $this->themeController->home($data, '');
        }
    }

    public function blog_list()
    {

        $this->check_blog_config();

        $data['title'] = config("SITE_TITLE") . " - Blog";
        $data['layout'] = 'site';
        $data['template'] = 'blogList';
        $data['meta'] = $this->getPageMetas([]);
        $data['blogs'] = $this->Page->where(['page_type_id' => 2, "status" => 1]);
        $data['list_variant'] = '';

        echo $this->themeController->blog_list($data);
    }

    public function blog_list_tag($tag)
    {
        $this->check_blog_config();

        $data['blogs'] = $this->Page->where(['page_type_id' => 2, "status" => 1]);

        if ($data['blogs']) {
            $data['blogs'] = $data['blogs']->filter(function ($value, $key) use ($tag) {
                return (isset($value->page_data['tags']) && in_array($tag, $value->page_data['tags']));
            });
        }

        $data['layout'] = 'site';
        $data['template'] = 'blogList';
        $data['list_variant'] = 'tag';
        $data['tag'] = $tag;
        $data['title'] = config("SITE_TITLE") . " - Blog Tags";

        $data['meta'] = $this->getPageMetas([]);
        echo $this->themeController->blog_list($data);
    }

    public function blog_list_author($author)
    {
        $this->check_blog_config();

        $this->load->model('Admin/User');
        
        // Sanitizar el autor
        $author = urldecode($author);
        
        $user = new User();
        $result = $user->find_with(['username' => $author]);

        if (!$result) {
            $this->error404();
            return;
        }

        $data['title'] = config("SITE_TITLE") . " - Blog";
        $data['layout'] = 'site';
        $data['template'] = 'blogList';
        $data['list_variant'] = 'author';
        $data['author_info'] = $user;

        //Filter Blogs by user_id
        $data['blogs'] = $this->Page->where(['page_type_id' => 2, "status" => 1, 'user_id' => $user->user_id]);
        $data['meta'] = $this->getPageMetas([]);
        echo $this->themeController->blog_list($data);
    }

    public function blog_list_search()
    {
        $this->check_blog_config();

        $this->load->model('Admin/User');
        $user = new User();

        // Obtener y sanitizar el término de búsqueda
        $term = $this->input->get("q", TRUE);
        
        // Validar que el término no esté vacío
        if (empty($term)) {
            $this->error404();
            return;
        }

        $result = $this->Page->search($term);

        if (!$result) {
            $this->error404();
            return;
        }

        $result = $result->filter(function ($value, $key) {
            return $value->page_type_id == 2;
        });

        $data['title'] = config("SITE_TITLE") . " - Blog";
        $data['layout'] = 'site';
        $data['template'] = 'blogList';
        $data['list_variant'] = 'author';
        $data['author_info'] = $user;

        //Filter Blogs by user_id
        $data['blogs'] = $result;
        $data['meta'] = $this->getPageMetas([]);
        echo $this->themeController->blog_list($data);
    }

    public function blog_list_categorie($categorie)
    {
        $this->check_blog_config();

        $this->load->model('Admin/Categories');
        
        // Sanitizar la categoría
        $categorie = urldecode($categorie);
        $categorie_name = ucwords(str_replace('-', ' ', $categorie));
        
        $categorie_obj = new Categories();
        $result = $categorie_obj->find_with(["name" => $categorie_name]);

        if (!$result) {
            $this->error404();
            return;
        }

        $data['title'] = config("SITE_TITLE") . " - Blog";
        $data['blogs'] = $this->Page->where(['page_type_id' => 2, "status" => 1, 'categorie_id' => $categorie_obj->categorie_id]);
        $data['blogs'] = $data['blogs'] ? $data['blogs'] : [];
        $data['layout'] = 'site';
        $data['categorie'] = $categorie_obj;
        $data['template'] = 'blogList';
        $data['list_variant'] = 'categorie';

        $data['meta'] = $this->getPageMetas([]);

        echo $this->themeController->blog_list($data);
    }

    private function check_blog_config()
    {

        if (config("SITE_ACTIVE_BLOGS") === "Off") {
            $this->error404();
            return false;
        }

        return true;
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
                $data['page'] = (object) ["title" => lang('form_submited_title'), "subtitle" => "", "content" => lang("form_submited_message")];
                echo $this->themeController->render($data, '');
            }
        } else {
            redirect("/");
        }
    }

    public function formajaxsubmit()
    {
        $this->lang->load('rest_lang', 'english');

        header('Content-Type: application/json');

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
                $response = array(
                    'code' => 200,
                    'data' => [],
                    "error_message" => '',
                    'requets_data' => $_POST,
                );

                $this->output->set_status_header(200);

                echo json_encode($response);
                return;
            }
        }

        // bad request
        $response = array(
            'code' => 400,
            'data' => [],
            "error_message" => lang('unexpected_error'),
            'requets_data' => $_POST,
        );

        $this->output->set_status_header(400);

        echo json_encode($response);

    }

    private function process_form_submit()
    {
        $this->load->model('Admin/SiteFormSubmit');
        $siteFormSubmit = new SiteFormSubmit();
        $siteFormSubmit->siteform_id = $this->input->post('form_reference');
        $siteFormSubmit->user_tracking_id = userdata('user_tracking_id');
        $siteFormSubmit->date_create = date("Y-m-d H:i:s");
        $siteFormSubmit->date_create = date("Y-m-d H:i:s");

        unset($_POST['form_reference']);
        $siteFormSubmit->status = 1;
        $siteFormSubmit->siteform_submit_data = $_POST;

        $result = $siteFormSubmit->save();

        set_notification("Recibido mensaje formulario", "Se recibió un registro en el formulario " . $this->input->post('form_reference'), "form_submit", "/admin/SiteForms/submit/#/details/" . $siteFormSubmit->siteform_submit_id);

        return $result;
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

        $data["related_pages"] = [];

        //Load local theme Controller
        echo $this->themeController->render($data);
    }

    public function siteMap()
    {
        if (config("SITE_ACTIVE_BLOGS") === "Off") {
            $data['pages'] = $this->Page->where(["status" => 1, "page_type_id" => 1]);
        } else {
            $data['pages'] = $this->Page->where(["status" => 1]);
        }

        header("Content-Type: text/xml;charset=iso-8859-1");
        echo $this->blade->view("admin.xml.sitemap", $data);
    }

    public function blogFeed()
    {
        $this->check_blog_config();

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
