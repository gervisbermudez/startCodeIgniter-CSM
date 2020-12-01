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

    function list()
    {
        $data['title'] = config("SITE_TITLE") . " - Blog";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        $data['blogs'] = $this->Page->where(['page_type_id' => 2, "status" => 1]);
        echo $this->blade->view("site.blogList", $data);
    }

    private function renderpage($page)
    {
        if ($page->map) {
            $data['page'] = $page;
            $data['meta'] = $this->getPageMetas($page);
            $data['title'] = config("SITE_TITLE") . " - " . $page->title;
            $data['layout'] = $page->layout == 'default' ? 'site' : $page->layout;
            $template = $page->template == 'default' ? 'template' : $page->template;
            if (getThemePath()) {
                $this->blade->changePath(getThemePath());
            }
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
