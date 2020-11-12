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
        $pageInfo = new Page();
        $result = $pageInfo->find_with(array('path' => $this->uri->uri_string(), 'status' => 1));

        if (!$result) {
            //Not found Page
            $this->error404();
            return;
        }

        //Is the page published?
        $date_now = new DateTime();
        $pagePublishTime = DateTime::createFromFormat('Y-m-d H:i:s', $pageInfo->date_publish);

        if ($date_now < $pagePublishTime) {
            //Not found Page
            $this->error404();
            return;
        }

        $data['page'] = $pageInfo;
        $data['meta'] = $this->getPageMetas($pageInfo);
        $data['title'] = $pageInfo->page_data["title"] ? $pageInfo->page_data["title"] : $pageInfo->title;
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        $data['headers_includes'] = isset($pageInfo->page_data["headers_includes"]) ? $pageInfo->page_data["headers_includes"] : "";
        $data['footer_includes'] = isset($pageInfo->page_data["footer_includes"]) ? $pageInfo->page_data["footer_includes"] : "";
        $template = $pageInfo->template == 'default' ? 'template' : $pageInfo->template;

        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }

        echo $this->blade->view("site.templates." . $template, $data);

    }

    public function preview()
    {
        $pageInfo = new Page();
        $result = $pageInfo->find_with(array('page_id' => $this->input->get('page_id'), 'status' => 2));

        if (!$result) {
            //Not found Page
            $this->error404();
            return;
        }

        $data['page'] = $pageInfo;
        $data['meta'] = $this->getPageMetas($pageInfo);

        $data['title'] = $pageInfo->page_data["title"] ? $pageInfo->page_data["title"] : $pageInfo->title;
        $data['headers_includes'] = isset($pageInfo->page_data["headers_includes"]) ? $pageInfo->page_data["headers_includes"] : "";
        $data['footer_includes'] = isset($pageInfo->page_data["footer_includes"]) ? $pageInfo->page_data["footer_includes"] : "";
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        $template = $pageInfo->template == 'default' ? 'template' : $pageInfo->template;

        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        
        echo $this->blade->view("site.templates." . $template, $data);

    }

}
