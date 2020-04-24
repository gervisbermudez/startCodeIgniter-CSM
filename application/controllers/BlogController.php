<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class BlogController extends Base_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Page');
    }

    public function list()
    {
        $data['title'] = "Modern Business - Blog";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.blogList", $data);

    }

    private function renderpage($pageInfo){
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

    public function get_blog($path)
    {
        $pages = new Page();
        $pageInfo = $pages->where(array('path' => 'blog/' . $path, 'status' => 1));
        $this->renderpage($pageInfo);
    }

    public function get_blog_categorie($categorie, $path)
    {
        $pages = new Page();
        $pageInfo = $pages->where(array('path' => 'blog/' . $categorie . '/' . $path, 'status' => 1));
        $this->renderpage($pageInfo);
    }

        public function get_blog_subcategorie($categorie, $subcategorie, $path)
    {
        $pages = new Page();
        $pageInfo = $pages->where(array('path' => 'blog/' . $categorie . '/' . $subcategorie . '/' . $path, 'status' => 1));
        $this->renderpage($pageInfo);
    }

}
