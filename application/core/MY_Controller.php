<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{
    public $routes_permisions = [];

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('admin/admin', 'english');

        if (!$this->session->userdata('logged_in')) {
            $uri = urlencode(uri_string());
            redirect('admin/login/?redirect=' . $uri);
        }
        if ($this->config->item('enable_profiler')) {
            $this->output->enable_profiler(true);
        }
    }

    public function check_permisions()
    {
        $uri_string = uri_string();
        $matches = [];
        foreach ($this->routes_permisions as $method => $route) {

            $patern = $route["patern"];
            $permissions = $route["required_permissions"];

            if (preg_match($patern, $uri_string, $matches)) {
                foreach ($permissions as $key => $value) {
                    if (!has_permisions($value)) {
                        $this->showError(lang('not_have_permissions'));
                        die();
                    }
                }
            }
        }
    }

    public function showError($errorMsg = 'Ocurrio un error inesperado', $data = array('title' => 'Error', 'h1' => 'Error'))
    {
        $data['conten'] = $errorMsg;
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.blankpage", $data);
    }

    public function error404()
    {

        $data['base_url'] = $this->config->base_url();
        $data['title'] = "404";
        $url = uri_string();
        if (stristr($url, 'admin') === false) {
            echo $this->blade->view("error404", $data);
        } else {
            if (!$this->session->userdata('logged_in')) {
                $uri = str_replace('/', '_', uri_string());
                redirect('login/index/' . $uri);
            } else {
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.blankpage", $data);
            }
        }

    }

}

class Base_Controller extends CI_Controller
{

    public $themeController = null;

    public function __construct()
    {
        parent::__construct();

        $this->load_config();
        $this->lang->load('site', 'english');
        $this->load->library('Track_Visitor');
        if (config('SITEM_TRACK_VISITORS') == 'Si') {
            $this->track_visitor->visitor_track();
        }
        //Load local theme Controller
        include getThemePath() . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'ThemeController.php';
        $this->themeController = new ThemeController();
    }

    public function getPageMetas($page)
    {
        if (isset($page->page_data["meta"])) {
            function convertArrayToObject($value)
            {
                $value = (Array) $value;
                if (isset($value['property']) && $value['property'] == 'og:image') {
                    if ($value['content'] != '') {
                        $value['content'] = base_url($value['content']);
                    } else {
                        $value['content'] = base_url('public/img/default.jpg');
                    }
                }
                if (isset($value['name']) && $value['name'] == 'twitter:image') {
                    if ($value['content'] != '') {
                        $value['content'] = base_url($value['content']);
                    } else {
                        $value['content'] = base_url('public/img/default.jpg');
                    }
                }
                return $value;
            }
            return array_map("convertArrayToObject", $page->page_data["meta"]);

        }

        $description = config('SITE_DESCRIPTION');
        $url = base_url(uri_string());
        $title = config('SITE_TITLE');

        if(isset($page->content)){
            $description = character_limiter(strip_tags($page->content), 120);
            $title = $page->title;
            $url = base_url($page->path);
        }
        
        if (isset($page->main_image)) {
            $imagen_url = base_url($page->main_image->file_front_path);
        } else {
            $imagen_url = base_url(getThemePublicPath() . 'images/default-brand.png');
        }

        $default_metas = array(
            array('name' => 'keywords', 'content' => $title),
            array('name' => 'description', 'content' => $description),
            array('name' => 'ROBOTS', 'content' => 'NOODP'),
            array('name' => 'GOOGLEBOT', 'content' => 'INDEX, FOLLOW'),
            array('property' => 'og:title', 'content' => $title),
            array('property' => 'og:description', 'content' => $description),
            array('property' => 'og:site_name', 'content' => config('SITE_TITLE')),
            array('property' => 'og:url', 'content' => $url),
            array('property' => 'og:image', 'content' => $imagen_url),
            array('property' => 'og:type', 'content' => $title),
            array('name' => 'twitter:card', 'content' => 'summary_large_image'),
            array('name' => 'twitter:site', 'content' => '@gervisbermudez'),
            array('name' => 'twitter:creator', 'content' => '@gervisbermudez'),
            array('name' => 'twitter:title', 'content' => $title),
            array('name' => 'twitter:description', 'content' => $description),
            array('name' => 'twitter:image', 'content' => $imagen_url),
        );
        return $default_metas;
    }

    public function error404()
    {
        $this->output->set_status_header(404);
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Page not found | 404";
        $url = uri_string();
        //is a public url ?
        if (stristr($url, 'admin') === false) {
            $page_id = config("SITE_ERROR_404_PAGE");
            if ($page_id) {
                $data = $this->get_page_info(array('page_id' => $page_id, 'status' => 1));
                if ($data == null) {
                    $this->error404();
                    return;
                }
                echo $this->themeController->render($data);
            } else {
                if (getThemePath()) {
                    $this->blade->changePath(getThemePath());
                }
                $this->load->database();
                $this->load->model('Admin/Page');
                $page = new Page();
                $result = $page->where(['status' => '1']);
                $data["pages"] = $result;
                echo $this->blade->view("site.error404", $data);
            }
        } else {
            if (!$this->session->userdata('logged_in')) {
                $uri = str_replace('/', '_', uri_string());
                redirect('login/index/' . $uri);
            } else {
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.blankpage", $data);
            }
        }

    }

    public function get_page_info($where)
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
        $data['title'] = isset($pageInfo->page_data["title"]) ? config("SITE_TITLE") . " - " . $pageInfo->page_data["title"] : config("SITE_TITLE") . " - " . $pageInfo->title;
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        $data['headers_includes'] = isset($pageInfo->page_data["headers_includes"]) ? $pageInfo->page_data["headers_includes"] : "";
        $data['footer_includes'] = isset($pageInfo->page_data["footer_includes"]) ? $pageInfo->page_data["footer_includes"] : "";
        $data['template'] = $pageInfo->template == 'default' ? 'templates.default' : $pageInfo->template;
        $this->load->model('Admin/Menu');
        $menu = new Menu();
        $menu->find_with(['menu_id' => 1]);
        $data["menu"] = $menu;
        return $data;
    }

    /**
     * Load especific config
     */
    private function load_config()
    {
        //Load Database config
        $config = $this->Site_config->all();
        $config = $config ? $config : [];
        foreach ($config as $value) {
            $this->config->set_item($value->config_name, $value->config_value);
        }

        $config = [];
        //Load especific theme config
        if (getThemePath()) {
            $configPath = getThemePath() . '/config/';
            $this->load->helper('directory');
            $map = directory_map($configPath);
            if ($map) {
                foreach ($map as $key => $file) {
                    if (strpos($file, '.php')) {
                        include $configPath . $file;
                        foreach ($config as $config_name => $config_value) {
                            $this->config->set_item($config_name, $config_value);
                        }
                    }
                }
            }

            if (isset($route)) {
                $this->router->routes = array_merge($this->router->routes, $route);
            }
        }

    }

}

/* End of file MY_Controller */
