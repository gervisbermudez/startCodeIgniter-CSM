<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PageController extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Page');

    }

    public function index()
    {
        $pages = new Page();
        $pageInfo = $pages->where(array('path' => $this->uri->uri_string(), 'status' => 1));
        if ($pageInfo) {
			$pageInfo = $pageInfo->first();
            $data['page'] = $pageInfo;
            $data['title'] = $pageInfo->title;
            $data['template'] = $pageInfo->template == 'default' ? 'site' : $pageInfo->template;
            echo $this->blade->view("site.template", $data);
        } else {
            $this->error404();
        }
    }

}
