<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class EventsController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->renderAdminView('admin.events.events_list', lang('menu_events'), lang('events_all'));
    }

    public function agregar()
    {
        $this->renderAdminView('admin.events.create_event', lang('events_new'), lang('events_add'), [
            'editMode' => 'new',
            'event_id' => null
        ]);
    }

    public function editar($event_id = "")
    {
        $this->load->model('Admin/EventModel');
        $event = $this->findOrFail(new EventModel(), $event_id, lang('events_not_found'));
        
        $this->renderAdminView('admin.events.create_event', lang('events_edit'), lang('events_add'), [
            'editMode' => 'edit',
            'event_id' => $event_id
        ]);
    }

}
