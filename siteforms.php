<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteForms extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['h1'] = "Todas los Formularios del Sitio";
        $data['title'] = ADMIN_TITLE . " | Formularios del Sitio";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.siteforms.siteforms_list", $data);
    }

    public function nuevo()
    {
        $data['title'] = ADMIN_TITLE . " | SiteForm";
        $data['h1'] = "Nuevo Formulario";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['siteform_id'] = '';
        $data['editMode'] = 'new';
        echo $this->blade->view("admin.siteforms.new_form", $data);
    }

    public function editar($siteform_id)
    {
        $data['title'] = ADMIN_TITLE . " | SiteForm";
        $data['h1'] = "Editar SiteForm";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['siteform_id'] = $siteform_id;
        $data['editMode'] = 'edit';
        echo $this->blade->view("admin.siteforms.new_form", $data);
    }

}
