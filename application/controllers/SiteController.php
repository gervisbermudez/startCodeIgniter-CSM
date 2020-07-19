<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteController extends Base_Controller
{

    public function index()
    {
       
       $this->load->model('Admin/Form_custom');
       $form = new Form_custom();
       $form->find(22);

       
       echo "<pre>";
       print_r ($form->as_data());
       echo "</pre>";
       
       
    }

    public function about()
    {
        $data['title'] = SITE_TITLE . " - About";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.about", $data);
    }

        public function services()
    {
        $data['title'] = SITE_TITLE . " - Services";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.services", $data);
    }

    public function contact()
    {
        $data['title'] = SITE_TITLE . " - Contact";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.contact", $data);
    }

    public function faq()
    {
        $data['title'] = SITE_TITLE . " - FAQ";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.faq", $data);
    }

    public function pricing()
    {
        $data['title'] = SITE_TITLE . " - FAQ";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.pricing", $data);
    }

    public function sidebar()
    {
        $data['title'] = SITE_TITLE . " - Sidebar";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.sidebar", $data);
    }

    public function fullWidth()
    {
        $data['title'] = SITE_TITLE . " - Full Width";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.fullwidth", $data);
    }

    public function blogPost()
    {
        $data['title'] = SITE_TITLE . " - Full Width";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.blogPost", $data);
    }

    public function portfolio()
    {
        $data['title'] = SITE_TITLE . " - Portfolio";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.portfolioList", $data);
    }

    public function portfolioItem()
    {
        $data['title'] = SITE_TITLE . " - Portfolio Item";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.portfolioItem", $data);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
