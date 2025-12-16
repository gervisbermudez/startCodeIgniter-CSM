<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Events extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Eventos";
        $data['h1'] = "Eventos";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.eventos.eventos_list", $data);

    }

    public function agregar()
    {
        $data['title'] = ADMIN_TITLE . " | Nuevo Evento";
        $data['h1'] = "Agregar nuevo Evento";
        $data['editMode'] = "new";
        $data['event_id'] = null;
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.eventos.crear_evento", $data);

    }

    public function editar($event_id = "")
    {

        $this->load->model('Admin/Event');
        $event = new Event();
        $result = $event->find($event_id);

        if ($result) {
            $data['title'] = ADMIN_TITLE . " | Nuevo Evento";
            $data['h1'] = "Agregar nuevo Evento";
            $data['editMode'] = "edit";
            $data['event_id'] = $event_id;
            $data['header'] = $this->load->view('admin/header', $data, true);
            echo $this->blade->view("admin.eventos.crear_evento", $data);
        } else {
            $this->error404();
        }

    }

}
