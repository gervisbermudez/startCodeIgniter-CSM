<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CalendarController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/calendar\/?$/',
            "required_permissions" => ["SELECT_CALENDAR"],
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
        $this->renderAdminView('admin.calendar.calendar', 'Calendario', 'Calendario');
    }

}
