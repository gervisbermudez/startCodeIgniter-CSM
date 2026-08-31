<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PageController extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/PageModel');
        $this->Page = new PageModel();
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

        $this->load->model('Admin/UserModel');
        
        // Sanitizar el autor
        $author = urldecode($author);
        
        $user = new UserModel();
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

        $this->load->model('Admin/UserModel');
        $user = new UserModel();

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

        $this->load->model('Admin/CategorieModel');
        
        // Sanitizar la categoría
        $categorie = urldecode($categorie);
        $categorie_name = ucwords(str_replace('-', ' ', $categorie));
        
        $categorie_obj = new CategorieModel();
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

    public function events_list()
    {
        $this->load->model('Admin/EventModel');
        $eventModel = new EventModel();
        $when = $this->input->get('when');

        $data['title'] = config("SITE_TITLE") . " - " . lang('events_heading');
        $data['layout'] = 'site';
        $data['template'] = 'eventsList';
        $data['meta'] = $this->getPageMetas([]);
        $data['when'] = $when;

        if ($when === 'past') {
            $data['events'] = $eventModel->past();
            $data['past_events'] = false;
        } else {
            $data['events'] = $eventModel->upcoming();
            $data['past_events'] = $eventModel->past();
        }

        echo $this->themeController->events_list($data);
    }

    public function get_event($slug = null)
    {
        if ($slug === null || $slug === '') {
            $slug = $this->uri->segment($this->uri->total_segments());
        }

        $this->load->model('Admin/EventModel');
        $event = new EventModel();
        if (!$event->find_by_slug($slug)) {
            $this->error404();
            return;
        }

        $data['title'] = config("SITE_TITLE") . " - " . $event->name;
        $data['layout'] = 'site';
        $data['template'] = 'event';
        $data['event'] = $event;
        $data['meta'] = $this->getPageMetas([]);

        echo $this->themeController->event_detail($data);
    }

    public function formsubmit()
    {
        $form = $this->resolve_posted_siteform();
        if (!$form) {
            redirect("/");
            return;
        }

        if ($this->is_form_cooldown($form->name)) {
            $data['title'] = config("SITE_TITLE") . " - " . lang('form_wait_title');
            $data['layout'] = 'site';
            $data['template'] = 'templates.default';
            $data['page'] = (object) array(
                "title" => lang('form_wait_title'),
                "subtitle" => "",
                "content" => lang('siteforms_wait'),
            );
            echo $this->themeController->render($data, '');
            return;
        }

        $this->process_form_submit($form);
        $this->merge_form_session($form->name);

        $data['title'] = config("SITE_TITLE") . " - Submited Form";
        $data['layout'] = 'site';
        $data['template'] = 'templates.default';
        $data['page'] = (object) array(
            "title" => lang('form_submited_title'),
            "subtitle" => "",
            "content" => lang("form_submited_message"),
        );
        echo $this->themeController->render($data, '');
    }

    public function formajaxsubmit()
    {
        $this->lang->load('rest_lang', 'english');
        header('Content-Type: application/json');

        $form = $this->resolve_posted_siteform();
        if (!$form) {
            $this->output->set_status_header(400);
            echo json_encode(array(
                'code' => 400,
                'data' => array(),
                "error_message" => lang('unexpected_error'),
                'requets_data' => $_POST,
            ));
            return;
        }

        if ($this->is_form_cooldown($form->name)) {
            $this->output->set_status_header(429);
            echo json_encode(array(
                'code' => 429,
                'data' => array(),
                "error_message" => lang('siteforms_wait'),
            ));
            return;
        }

        $this->process_form_submit($form);
        $this->merge_form_session($form->name);

        $this->output->set_status_header(200);
        echo json_encode(array(
            'code' => 200,
            'data' => array(),
            "error_message" => '',
        ));
    }

    /**
     * @return object|null
     */
    private function resolve_posted_siteform()
    {
        $siteform_id = $this->input->post('form_reference');
        if (!$siteform_id) {
            return null;
        }
        $this->load->model('Admin/SiteFormModel');
        $siteform = new SiteFormModel();
        if (!$siteform->find($siteform_id)) {
            return null;
        }
        return $siteform;
    }

    /**
     * @param string $formName
     * @return bool
     */
    private function is_form_cooldown($formName)
    {
        $siteforms = $this->session->userdata('siteforms');
        if (!is_array($siteforms) || !isset($siteforms[$formName]) || empty($siteforms[$formName]['timestamp'])) {
            return false;
        }
        $last = DateTime::createFromFormat('Y-m-d H:i:s', $siteforms[$formName]['timestamp']);
        if (!$last) {
            return false;
        }
        $interval = (new DateTime())->diff($last);
        $minutes = (int) $interval->i + ((int) $interval->h * 60) + ((int) $interval->days * 24 * 60);
        return $minutes < 3;
    }

    /**
     * @param string $formName
     * @return void
     */
    private function merge_form_session($formName)
    {
        $siteforms = $this->session->userdata('siteforms');
        if (!is_array($siteforms)) {
            $siteforms = array();
        }
        $prev = isset($siteforms[$formName]['submited']) ? (int) $siteforms[$formName]['submited'] : 0;
        $siteforms[$formName] = array(
            'submited' => $prev + 1,
            'timestamp' => date('Y-m-d H:i:s'),
        );
        $this->session->set_userdata('siteforms', $siteforms);
    }

    /**
     * @param object $siteform
     * @return mixed
     */
    private function process_form_submit($siteform)
    {
        $form_reference = $this->input->post('form_reference');
        $this->load->model('Admin/SiteFormSubmitModel');
        $siteFormSubmit = new SiteFormSubmitModel();
        $siteFormSubmit->siteform_id = $form_reference;
        $siteFormSubmit->user_tracking_id = userdata('user_tracking_id');
        $siteFormSubmit->date_create = date("Y-m-d H:i:s");
        $siteFormSubmit->status = 1;

        $allowed = array();
        if (!empty($siteform->siteform_items)) {
            foreach ($siteform->siteform_items as $item) {
                if (isset($item->item_name) && $item->item_name !== '') {
                    $allowed[$item->item_name] = true;
                }
            }
        }

        $data = array();
        foreach ($_POST as $key => $value) {
            if ($key === 'form_reference') {
                continue;
            }
            if (isset($allowed[$key])) {
                $data[$key] = $value;
            }
        }
        $siteFormSubmit->siteform_submit_data = $data;

        $result = $siteFormSubmit->save();

        if (siteform_should_notify($siteform)) {
            $form_label = $siteform->name ? $siteform->name : $form_reference;
            set_notification(
                lang('notification_form_submit_title'),
                sprintf(lang('notification_form_submit_desc'), $form_label),
                'form_submit',
                'admin/siteforms/submit/#/details/' . $siteFormSubmit->siteform_submit_id
            );
        }

        return $result;
    }

    public function preview()
    {
        // Check user logged first
        if (!$this->session->userdata('logged_in')) {
            $uri = urlencode(uri_string());
            redirect('/');
        }

        // Get page_id from request
        $page_id = $this->input->get('page_id');
        
        // First check if the page exists and is not deleted
        $page = $this->Page->find($page_id);
        if (!$page || $page->status == 0) {
            $this->error404();
            return;
        }

        $data = $this->get_page_info(array('page_id' => $page_id));
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
