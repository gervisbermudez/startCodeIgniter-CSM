<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteController extends Base_Controller
{

    public function index()
    {
        $data['title'] = "Modern Business - Start Bootstrap Template";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.home", $data);
    }

    public function about()
    {
        $data['title'] = "Modern Business - About";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.about", $data);
    }

        public function services()
    {
        $data['title'] = "Modern Business - Services";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.services", $data);
    }

    public function contact()
    {
        $data['title'] = "Modern Business - Contact";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.contact", $data);
    }

    public function faq()
    {
        $data['title'] = "Modern Business - FAQ";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.faq", $data);
    }

    public function pricing()
    {
        $data['title'] = "Modern Business - FAQ";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.pricing", $data);
    }

    public function sidebar()
    {
        $data['title'] = "Modern Business - Sidebar";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.sidebar", $data);
    }

    public function fullWidth()
    {
        $data['title'] = "Modern Business - Full Width";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.fullwidth", $data);
    }

    public function blogPost()
    {
        $data['title'] = "Modern Business - Full Width";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.blogPost", $data);
    }

    public function portfolio()
    {
        $data['title'] = "Modern Business - Portfolio";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.portfolioList", $data);
    }

    public function portfolioItem()
    {
        $data['title'] = "Modern Business - Portfolio Item";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.portfolioItem", $data);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
