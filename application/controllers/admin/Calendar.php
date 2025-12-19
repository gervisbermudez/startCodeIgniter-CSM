<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Calendar extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->renderAdminView('admin.calendar.calendar', 'Calendario', 'Calendario');
    }

}
