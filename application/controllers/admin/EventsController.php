<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class EventsController extends MY_Controller
{

    public $routes_permisions = [
        "index" => [
            "patern" => '/admin\/events/',
            "required_permissions" => ["SELECT_EVENTS"],
            "conditions" => [],
        ],
        "agregar" => [
            "patern" => '/admin\/events\/add/',
            "required_permissions" => ["CREATE_EVENT"],
            "conditions" => [],
        ],
        "editar" => [
            "patern" => '/admin\/events\/edit\/(\d+)/',
            "required_permissions" => ["UPDATE_EVENT"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
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
