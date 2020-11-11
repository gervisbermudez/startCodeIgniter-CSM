<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteController extends Base_Controller
{

    public function index()
    {

        $data['title'] = config("SITE_TITLE") . " - Home";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.home", $data);
    }

    public function about()
    {
        $data['title'] = config("SITE_TITLE") . " - About";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.about", $data);
    }

    public function services()
    {
        $data['title'] = config("SITE_TITLE") . " - Services";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.services", $data);
    }

    public function contact()
    {
        $data['title'] = config("SITE_TITLE") . " - Contact";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.contact", $data);
    }

    public function faq()
    {
        $data['title'] = config("SITE_TITLE") . " - FAQ";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.faq", $data);
    }

    public function pricing()
    {
        $data['title'] = config("SITE_TITLE") . " - FAQ";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.pricing", $data);
    }

    public function sidebar()
    {
        $data['title'] = config("SITE_TITLE") . " - Sidebar";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.sidebar", $data);
    }

    public function fullWidth()
    {
        $data['title'] = config("SITE_TITLE") . " - Full Width";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.fullwidth", $data);
    }

    public function blogPost()
    {
        $data['title'] = config("SITE_TITLE") . " - Full Width";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.blogPost", $data);
    }

    public function portfolio()
    {
        $data['title'] = config("SITE_TITLE") . " - Portfolio";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.portfolioList", $data);
    }

    public function portfolioItem()
    {
        $data['title'] = config("SITE_TITLE") . " - Portfolio Item";
        if (getThemePath()) {
            $this->blade->changePath(getThemePath());
        }
        echo $this->blade->view("site.portfolioItem", $data);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
