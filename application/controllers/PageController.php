<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PageController extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Page');
        $config['enable_profiler'] = true;

    }

    public function index()
    {
        $pages = new Page();
        $pageInfo = $pages->where(array('path' => $this->uri->uri_string(), 'status' => 1));

        if (!$pageInfo) {
            //Not found Page
            $this->error404();
            return;
        }

        //Is the page published?
        $pageInfo = $pageInfo->first();
        $date_now = new DateTime();
        $pagePublishTime = DateTime::createFromFormat('Y-m-d H:i:s', $pageInfo->date_publish);

        if ($date_now < $pagePublishTime) {
            //Not found Page
            $this->error404();
            return;
        }

        $data['page'] = $pageInfo;
        $data['meta'] = $this->getPageMetas($pageInfo);

        $data['title'] = $pageInfo->title;
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        $template = $pageInfo->template == 'default' ? 'template' : $pageInfo->template;

        echo $this->blade->view("site.templates." . $template, $data);

    }

    public function preview()
    {
        $pages = new Page();
        $pageInfo = $pages->where(array('page_id' => $this->input->get('page_id'), 'status' => 2));

        if (!$pageInfo) {
            //Not found Page
            $this->error404();
            return;
        }
        //Is the page published?
        $pageInfo = $pageInfo->first();
        $data['page'] = $pageInfo;
        $data['meta'] = $this->getPageMetas($pageInfo);
        $data['title'] = $pageInfo->title;
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        $template = $pageInfo->template == 'default' ? 'template' : $pageInfo->template;

        echo $this->blade->view("site.templates." . $template, $data);

    }

}
