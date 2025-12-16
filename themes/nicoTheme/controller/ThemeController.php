<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ThemeController extends ThemeController_Base
{
    private $ci;

    public function __construct()
    {
        $this->blade = new Blade();
        $this->ci = &get_instance();
    }

    public function home($data)
    {
        $data['blogs'] = $this->ci->Page->where(['page_type_id' => 2, "status" => 1]);
        return $this->render($data, '');
    }

    public function blog_list($data)
    {
        $data['tags'] = $this->getTags();
        $data['categories'] = $this->getCategories();

        return $this->render($data);
    }

    public function blog_post($data)
    {
        $data['tags'] = $this->getTags($data["page"]);
        $data['categories'] = $this->getCategories();
        $data['blogs'] = $this->getRecentsBlogs($data["page"]);

        return $this->render($data);
    }

    private function getRecentsBlogs($currentPage)
    {
        $this->ci->load->model('Admin/Page');
        return $this->ci->Page->where(['page_type_id' => 2, "status" => 1, "page_id != " => $currentPage->page_id], 3);
    }

    private function getCategories()
    {
        $this->ci->load->model('Admin/Categories');
        $categorie = new Categories();
        $data['categories'] = $categorie->where(array('parent_id' => '0', 'type' => "page"));
        return $data['categories'] ? $data['categories'] : [];
    }

    private function getTags($page = null)
    {
        $this->ci->load->model('Admin/Page');
        $where = ['page_type_id' => 2, "status" => 1];
        if ($page) {
            $where["page_id !="] = $page->page_id;
        }

        $data['blogs'] = $this->ci->Page->where($where, 3);
        return $this->ci->Page->get_cloud_tags();
    }

    /**
     * @return String
     */
    public function render($data)
    {

        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        return $this->blade->view("site." . $data["template"], $data);
    }

}