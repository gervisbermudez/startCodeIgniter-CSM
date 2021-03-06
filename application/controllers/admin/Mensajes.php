<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mensajes extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Messages');
    }

    public function index($folder = 'Inbox')
    {
        $data['username'] = $this->session->userdata('username');
        $data['title'] = ADMIN_TITLE . " | Mensajes";
        $data['folder'] = $folder;
        $data['h1'] = "";
        $data['header'] = '';
        $data['mensajes'] = $this->Messages->all();
        $data['list'] = $this->load->view('admin/mensajes/list', $data, true);
        $data['head_includes'] = array(link_tag('public/css/admin/messages.css'));
        $data['footer_includes'] = array('<script src="' . base_url('public/js/chips.js') . '"></script>', '<script src="' . base_url('public/js/mensajes.min.js') . '"></script>');

        echo $this->blade->view("admin.mensajes.listado", $data);

    }
    public function folder($folder = 'Inbox')
    {
        $this->load->helper('text');
        $data['username'] = $this->session->userdata('username');
        $data['title'] = ADMIN_TITLE . " | Mensajes";
        $data['h1'] = "";
        $data['header'] = '';
        $data['folder'] = $folder;
        $data['head_includes'] = array('messages.css' => link_tag('public/css/admin/messages.css'));
        $data['mensajes'] = $this->Messages->get_mensaje(array('namefolder' => $folder));
        if (!$data['mensajes']) {
            $data['mensajes'] = $this->Messages->get_mensaje(array('namefolder' => 'Inbox'));
            $data['folder'] = 'Inbox';
        }
        $data['list'] = $this->load->view('admin/mensajes/list', $data, true);
        $data['footer_includes'] = array('tinymce' => '<script src="' . base_url('public/js/chips.js') . '"></script>');
        $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/mensajes/todos', $data);
        $this->load->view('admin/footer', $data);
    }
    public function ver($id = '')
    {
        $data['mensajes'] = $this->Messages->get_mensaje('all');
        $this->load->helper('array');
        $this->load->library('parser');
        $quotes = array(" indigo", "blue", " cyan", "green", "pink", "lime", 'orange');
        $deep = array("darken-1", 'accent-4', '');
        $mensaje = $this->Messages->get_mensaje(array('mensajes.id' => $id))[0];
        $this->Messages->set_mensaje_as_read($id);
        $data = array('_subject' => $mensaje['_subject'],
            'color' => random_element($quotes) . ' ' . random_element($deep),
            'initial_letter' => substr($mensaje['_from'], 0, 1),
            '_from' => $mensaje['_from'], '_mensaje' => $mensaje['_mensaje'], 'mensaje-id' => $id);
        $data['preview'] = $this->parser->parse('admin/mensajes/preview', $data, true);
        $data['mensajes'] = $this->Messages->get_mensaje('all');
        $data['list'] = $this->load->view('admin/mensajes/list', $data, true);
        $data['base_url'] = $this->config->base_url();
        $this->load->helper('text');
        $data['username'] = $this->session->userdata('username');
        $data['title'] = ADMIN_TITLE . " | Mensajes";
        $data['h1'] = "";
        $data['header'] = ''; //$this->load->view('admin/header', $data, TRUE);
        $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/mensajes/todos', $data);
        $this->load->view('admin/footer', $data);
    }
    public function getfolder($foldername = 'Inbox')
    {
        $data['base_url'] = $this->config->base_url();
        $this->load->helper('text');
        $data['mensajes'] = $this->Messages->get_mensaje(array('folder' => $foldername));
        $this->load->view('admin/mensajes/list', $data);
    }
    public function showError($errorMsg = 'Ocurrio un error inesperado', $data = array('title' => 'Error', 'h1' => 'Error'))
    {
        $data['base_url'] = $this->config->base_url();
        $data['conten'] = $errorMsg;
        $data['header'] = $this->load->view('admin/header', $data, true);
        $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/template', $data);
        $this->load->view('admin/footer', $data);
    }
    public function get_mensaje_by_ajax()
    {

        $this->load->helper('array');
        $id_mensaje = $this->input->post('id_mensaje');
        $data['mensajes'] = $this->Messages->get_mensaje(array('mensajes.id' => $id_mensaje));
        $this->Messages->set_mensaje_as_read($id_mensaje);
        $preview = $this->load->view('admin/mensajes/preview', $data, true);
        $this->output->set_output($preview);
    }
    public function Send()
    {
        if ($this->input->post('email')) {
            $this->load->helper('date');
            $datestring = '%Y-%m-%d %h:%i:%a';
            $time = time();
            $fecha = mdate($datestring, $time);
            echo "$fecha";
            $this->Messages->set_Messages();
        }
    }
    public function update_messagesfolder_byajax()
    {
        $id = $this->input->post('id');
        $folder = $this->input->post('folder');
        $datawhere = array('id' => $id);
        $arraydata = array('folder' => $folder);
        $data['mensajes'] = $this->Messages->update_mensajefolder($arraydata, $datawhere);
        $json = array('result' => false);
        if ($data['mensajes']) {
            $json = array('result' => true);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
}
