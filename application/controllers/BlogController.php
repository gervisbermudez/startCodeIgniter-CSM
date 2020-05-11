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

    private function renderpage($page){
        if ($page) {
            $data['page'] = $page;
            $data['meta'] = $this->getPageMetas($page);
            $data['title'] = $page->title;
            $data['layout'] = $page->layout == 'default' ? 'site' : $page->layout;
            $template = $page->template == 'default' ? 'template' : $page->template;
            echo $this->blade->view("site.templates." . $template, $data);
        } else {
            $this->error404();
        }
    }

    public function get_blog($path)
    {
        $pages = new Page();
        $pages->find_with(array('path' => 'blog/' . $path, 'status' => 1));
        $this->renderpage($pages);
    }

    public function get_blog_categorie($categorie, $path)
    {
        $pages = new Page();
        $pages->find_with(array('path' => 'blog/' . $categorie . '/' . $path, 'status' => 1));
        $this->renderpage($pages);
    }

        public function get_blog_subcategorie($categorie, $subcategorie, $path)
    {
        $pages = new Page();
        $pages->find_with(array('path' => 'blog/' . $categorie . '/' . $subcategorie . '/' . $path, 'status' => 1));
        $this->renderpage($pages);
    }

}
