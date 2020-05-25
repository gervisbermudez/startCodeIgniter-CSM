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
        $data['forms_list'] = $this->Form_custom->get_all();
        echo $this->blade->view("admin.formularios.formularios_list", $data);
    }

    public function data()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Content";
        $data['h1'] = "Content";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('public/js/components/formContent.min.js?v=' . SITEVERSION) . "></script>");
        echo $this->blade->view("admin.formularios.data_list", $data);
    }

    function new () {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
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
        $data['title'] = ADMIN_TITLE . " | Formularios";
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
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('resources/components/dataFormModule.js?v=' . SITEVERSION) . "></script>");
        $data['form_id'] = $form_id;
        $data['form_content_id'] = false;

        $data['editMode'] = false;

        echo $this->blade->view("admin.formularios.dataForm", $data);
    }

    public function editData($form_id, $form_content_id)
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Formularios";
        $data['h1'] = "Formularios";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['footer_includes'] = array("<script src=" . base_url('resources/components/dataFormModule.js?v=' . SITEVERSION) . "></script>");
        $data['form_id'] = $form_id;
        $data['form_content_id'] = $form_content_id;
        $data['editMode'] = true;

        echo $this->blade->view("admin.formularios.dataForm", $data);
    }

    public function deleteForm($form_id)
    {
        $this->Form_custom->delete_form($form_id);
        redirect('admin/formularios');

    }

    public function saveForm()
    {
        $this->output->enable_profiler(false);
        $data = (json_decode($_POST['data']));
        $response = array(
            'code' => 200,
            'data' => json_encode($this->Form_custom->save_form($data)),
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
            'data' => json_encode($this->Form_custom->save_data_form($data)),
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function updateDataForm()
    {
        $this->output->enable_profiler(false);
        $data = (json_decode($_POST['data']));
        //Update last data
        $update_where = array('form_content_id' => $data->form_content_id);
        $update_data = array('status' => 2);
        $this->Form_custom->update_data($update_where, $update_data, 'form_content_data');

        $response = array(
            'code' => 200,
            'data' => $this->Form_custom->save_data_form($data),
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function updateForm()
    {
        $this->output->enable_profiler(false);
        $data = (json_decode($_POST['data']));
        $response = array(
            'code' => 200,
            'data' => json_encode($this->Form_custom->update_form($data)),
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function get_form_info($form_id)
    {
        $this->output->enable_profiler(false);
        $result = $data['forms_list'] = $this->Form_custom->get_forms(
            'where fc.form_custom_id =' . $form_id
        );

        foreach ($result as $key => &$value) {
            $value->fields_data = str_replace('\"', '"', $value->fields_data);
            $value->fields_data = str_replace('"{', '{', $value->fields_data);
            $value->fields_data = str_replace('}"', '}', $value->fields_data);
            $value->fields_data = json_decode($value->fields_data);
        }

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function ajax_get_forms_types()
    {
        $this->output->enable_profiler(false);
        $result = $data['forms_list'] = $this->Form_custom->get_form_types();

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }

    public function ajax_get_forms_data()
    {
        $this->output->enable_profiler(false);

        $result = $this->Form_custom->get_all();
        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
        } else {
            $response = array(
                'code' => 404,
                'data' => [],
            );

        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

    }
}
