<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Formularios extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Form_custom');
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.formularios.formularios_list", $data);
    }

    public function new () {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.formularios.new", $data);
    }

    public function editForm($form_custom_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = userdata('username');
        $data['form_custom_id'] = $form_custom_id;
        echo $this->blade->view("admin.formularios.new", $data);
    }

    public function content()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Content";
        $data['h1'] = "Content";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.formularios.content_list", $data);
    }

    public function addData($form_custom_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);

        $data['form_custom_id'] = $form_custom_id;
        $data['form_content_id'] = false;
        $data['editMode'] = false;

        echo $this->blade->view("admin.formularios.formContent", $data);
    }

    public function editData($form_custom_id, $form_content_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['form_custom_id'] = $form_custom_id;
        $data['form_content_id'] = $form_content_id;
        $data['editMode'] = true;
        echo $this->blade->view("admin.formularios.formContent", $data);
    }
}
