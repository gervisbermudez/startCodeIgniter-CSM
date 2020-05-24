<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Configuration Controller
 */
class Configuracion extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Configuracion";
        $data['h1'] = "Configuracion";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['action'] = base_url('admin/configuracion/save');
        $realthemes = get_dir_file_info('./themes/');
        $data['themes_'] = array_filter($realthemes, 'isDir');
        $data['config'] = $this->Site_config->load_config();
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
        $this->Site_config->save_config($data);
        $data['base_url'] = $this->config->base_url();
        $data['title'] = ADMIN_TITLE . " | Configuracion";
        $data['h1'] = "Configuracion";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['username'] = $this->session->userdata('username');
        $data['action'] = base_url('admin/configuracion/save');
        $realthemes = get_dir_file_info('./themes/');
        $data['themes_'] = array_filter($realthemes, 'isDir');
        $data['saved'] = 'Configuracion guardada';
        $data['config'] = $this->Site_config->load_config();

        echo $this->blade->view("admin.configuracion.all_config", $data);

    }

}
