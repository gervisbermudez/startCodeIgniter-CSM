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
        $this->renderAdminView('admin.events.events_list', 'Eventos', 'Eventos');
    }

    public function agregar()
    {
        $this->renderAdminView('admin.events.create_event', 'Nuevo Evento', 'Agregar nuevo Evento', [
            'editMode' => 'new',
            'event_id' => null
        ]);
    }

    public function editar($event_id = "")
    {
        $this->load->model('Admin/Event');
        $event = $this->findOrFail(new Event(), $event_id, 'Evento no encontrado');
        
        $this->renderAdminView('admin.events.crear_evento', 'Nuevo Evento', 'Agregar nuevo Evento', [
            'editMode' => 'edit',
            'event_id' => $event_id
        ]);
    }

}
