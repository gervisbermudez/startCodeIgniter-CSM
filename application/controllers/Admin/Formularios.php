<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Formularios extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Forms_model');     
    }

    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['forms_list'] = $this->Forms_model->get_all();
        echo $this->blade->view("admin.formularios.formularios_list", $data);
    }

    public function new()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('public/js/draggable/dist/js/jquery.dragsort.min.js?v=' . SITEVERSION) . "></script>", 
        "<script src=" . base_url('resources/components/formModule.js?v=' . SITEVERSION) . "></script>");

        echo $this->blade->view("admin.formularios.new", $data);
    }

    public function editForm($form_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('public/js/draggable/dist/js/jquery.dragsort.min.js?v=' . SITEVERSION) . "></script>", 
        "<script src=" . base_url('resources/components/formModule.js?v=' . SITEVERSION) . "></script>");
        $data['form_id'] = $form_id;
        
        echo $this->blade->view("admin.formularios.new", $data);
    }

    public function addData($form_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('resources/components/dataFormModule.js?v=' . SITEVERSION) . "></script>");
        $data['form_id'] = $form_id;
        $data['editMode'] = false;
        
        echo $this->blade->view("admin.formularios.dataForm", $data);
    }

    public function deleteForm($form_id)
    {
        $this->Forms_model->delete_form($form_id);
        redirect('admin/formularios');

    }

    public function saveForm()
    {
        $this->output->enable_profiler(false);
        //print_r(json_decode($_POST['data']));
        $data = (json_decode($_POST['data']));
        $response = array(
            'code' => 200,
            'data' => json_encode($this->Forms_model->save_form($data)),
        );

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }

    public function saveDataForm()
    {
        $this->output->enable_profiler(false);
        //print_r(json_decode($_POST['data']));
        $data = (json_decode($_POST['data']));
        $response = array(
            'code' => 200,
            'data' => json_encode($this->Forms_model->save_data_form($data)),
        );

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));  
    }

    public function updateForm()
    {
        $this->output->enable_profiler(false);
        //print_r(json_decode($_POST['data']));
        $data = (json_decode($_POST['data']));
        $response = array(
            'code' => 200,
            'data' => json_encode($this->Forms_model->update_form($data)),
        );

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }

    public function get_form_info($form_id)
    {
        $this->output->enable_profiler(false);
        $result = $data['forms_list'] = $this->Forms_model->get_forms(
            'where id =' . $form_id
        );

        foreach ($result as $key => &$value) {
            $value['fields_data'] = str_replace('\"', '"', $value['fields_data']);
            $value['fields_data'] = str_replace('"{', '{', $value['fields_data']);
            $value['fields_data'] = str_replace('}"', '}', $value['fields_data']);
            $value['fields_data'] = json_decode($value['fields_data']);
        }

        $response = array(
            'code' => 200,
            'data' => $result
        );

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }
}
