<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class NotificationsController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->renderAdminView('admin.notifications.list', lang('notifications_title'), lang('notifications_all'));
    }
}
