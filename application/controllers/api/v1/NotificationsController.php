<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class NotificationsController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');

        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->model('Admin/NotificationsModel');
    }

    /**
     * GET /api/v1/notifications?status=1|2|all
     * Default status=1 (unread). Always data: [].
     */
    public function index_get()
    {
        $status = $this->input->get('status');
        if ($status === null || $status === '') {
            $status = 1;
        }
        $limit = $this->input->get('limit');
        if ($status === 'all') {
            $limit = $limit ? (int) $limit : 100;
        } else {
            $limit = $limit ? (int) $limit : 20;
        }

        $model = new NotificationsModel();
        $rows = $model->inbox(userdata('user_id'), $status, $limit);
        $this->response_ok($rows ? $rows : array());
    }

    /**
     * GET /api/v1/notifications/count
     */
    public function count_get()
    {
        $model = new NotificationsModel();
        $this->response_ok(array(
            'unread' => $model->unread_count(userdata('user_id')),
        ));
    }

    /**
     * POST /api/v1/notifications/read/{id}
     */
    public function read_post($id = null)
    {
        $model = new NotificationsModel();
        if ($model->mark_read($id, userdata('user_id'))) {
            $this->response_ok($model);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    /**
     * POST /api/v1/notifications/read-all
     */
    public function read_all_post()
    {
        $model = new NotificationsModel();
        $model->mark_all_read(userdata('user_id'));
        $this->response_ok(array('ok' => true));
    }
}
