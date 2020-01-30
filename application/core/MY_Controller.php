<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $uri = str_replace('/', '_', uri_string());
            redirect('admin/login/index/' . $uri);
        }
        if ($this->config->item('enable_profiler')) {
            $this->output->enable_profiler(true);
        }

    }

    public function showError($errorMsg = 'Ocurrio un error inesperado', $data = array('title' => 'Error', 'h1' => 'Error'))
    {
        $data['conten'] = $errorMsg;
        $data['header'] = $this->load->view('admin/header', $data, true);
        $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/template', $data);
        $this->load->view('admin/footer', $data);
    }

    public function fn_ajax_delete_data()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $json = array('result' => false, 'message' => 'Error al eliminar datos');

        if ($this->StModel->delete_data(array('id' => $id), $table)) {
            $json = array('result' => true, 'message' => 'Datos eliminados con exito!');
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    public function fn_ajax_check_value()
    {
        $this->load->model('StModel');
        $table = $this->input->post('table');
        $campo = $this->input->post('campo');
        $valor = $this->input->post('valor');
        $res = $this->UserMod->get_is_exist_value($table, $campo, $valor);
        $json = array('result' => false);
        if ($res) {
            $json = array('result' => true);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    public function fn_ajax_change_state()
    {

        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $status = 0;
        if ($this->input->post('status') === '1') {
            $status = 1;
        }
        $json = array('result' => false, 'message' => 'Error al cambiar estado!');
        if ($this->StModel->update_data(array('id' => $id), array('status' => $status), $table)) {
            $json = array('result' => true, 'message' => 'Actualizado con exito!');
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }

    public function error404()
    {

        $data['base_url'] = $this->config->base_url();
        $data['title'] = "404";
        $url = uri_string();
        if (stristr($url, 'admin') === false) {
            echo $this->blade->view("error404", $data);
        } else {
            if (!$this->session->userdata('logged_in')) {
                $uri = str_replace('/', '_', uri_string());
                redirect('login/index/' . $uri);
            } else {
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                $this->load->view('admin/head', $data);
                $this->load->view('admin/navbar', $data);
                $this->load->view('admin/template', $data);
                $this->load->view('admin/footer', $data);
            }
        }

    }

}
/* End of file MY_Controller */
