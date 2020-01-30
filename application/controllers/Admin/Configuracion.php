<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Configuracion extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Configuracion";
        $data['h1'] = "Configuracion";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['action'] = base_url('admin/configuracion/save');
        $realthemes = get_dir_file_info('./themes/');
        $data['themes_'] = array_filter($realthemes, 'isDir');
        $data['config'] = $this->Config_model->load_config();
        echo $this->blade->view("admin.configuracion.all_config", $data);
    }

    public function save()
    {
        $data = array(
            array(
                'config_name' => 'site_theme',
                'config_value' => $this->input->post('theme_selected'),
            ),
        );
        $this->Config_model->save_config($data);
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Configuracion";
        $data['h1'] = "Configuracion";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['action'] = base_url('admin/configuracion/save');
        $realthemes = get_dir_file_info('./themes/');
        $data['themes_'] = array_filter($realthemes, 'isDir');
        $data['saved'] = 'Configuracion guardada';
        $data['config'] = $this->Config_model->load_config();

        echo $this->blade->view("admin.configuracion.all_config", $data);

    }

}
