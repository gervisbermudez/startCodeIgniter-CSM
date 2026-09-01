<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CalendarController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/(calendar|events\/calendar)\/?$/',
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
        $uri = trim(uri_string(), '/');
        if ($uri === 'admin/calendar') {
            redirect('admin/events/calendar');
            return;
        }

        $this->renderAdminView('admin.calendar.calendar', lang('menu_events'), lang('menu_calendar'));
    }

}
