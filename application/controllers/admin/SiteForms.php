<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Siteforms extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->renderAdminView('admin.siteforms.siteforms_list', 'Formularios del Sitio', 'Todas los Formularios del Sitio');
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.siteforms.new_form', 'Formularios del Sitio', 'Nuevo Formulario', [
            'siteform_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($siteform_id)
    {
        $this->renderAdminView('admin.siteforms.new_form', 'Formularios del Sitio', 'Editar SiteForm', [
            'siteform_id' => $siteform_id,
            'editMode' => 'edit'
        ]);
    }

    public function submit()
    {
        $this->renderAdminView('admin.siteforms.siteform_submit_list', 'Formularios recibidos del Sitio', 'Todas los Formularios del Sitio');
    }

}