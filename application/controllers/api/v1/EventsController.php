<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class EventsController extends REST_Controller
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

        $this->load->database();
        $this->load->helper('general');
        $this->load->helper('url');
        $this->load->model('Admin/EventModel');
    }

    /**
     * @api {get} /api/v1/events/:event_id Event list or one event
     * @apiName GetEvents
     * @apiGroup Events
     *
     * @apiParam {Number} [event_id] Event unique ID.
     * @apiParam {Number} [status] Filter by status.
     * @apiParam {String} [when=all] upcoming|past|all
     */
    public function index_get($event_id = null)
    {
        if (!$this->require_event_permision('SELECT_EVENTS')) {
            return;
        }

        $event = new EventModel();
        if ($event_id) {
            $result = $event->find_with(array('event_id' => $event_id));
            $result = $result ? $event->as_data() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $status = $this->get('status');
        $when = $this->get('when');
        $where = array();
        $options = array('unfiltered' => true, 'use_get_all' => true);
        if ($status !== null && $status !== '') {
            $where['status'] = $status;
            $options = array();
        }
        if ($when === 'upcoming' || $when === 'past') {
            $where['_when'] = $when;
            $options['use_get_all'] = false;
        }
        $this->respond_index_list($event, $where, array('date_start', 'DESC'), $options);
    }

    /**
     * Create or update. Does not overwrite date_create on update.
     *
     * @return void
     */
    public function index_post()
    {
        $event_id = $this->input->post('event_id');
        $is_update = ($event_id !== null && $event_id !== '' && $event_id !== false);

        if (!$this->require_event_permision($is_update ? 'UPDATE_EVENT' : 'CREATE_EVENT')) {
            return;
        }

        if ($this->input->post('location_type') === null || $this->input->post('location_type') === '') {
            $_POST['location_type'] = 'physical';
        }

        $this->load->library('FormValidator');
        $form = new FormValidator();
        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[1]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
            array('field' => 'location_type', 'label' => 'location_type', 'rules' => 'in_list[physical,online,hybrid]'),
        );
        $form->set_rules($config);

        $errors = array();
        if (!$form->run()) {
            $errors = $form->_error_array;
        }

        $status = (int) $this->input->post('status');
        if (!in_array($status, array(1, 2, 3), true)) {
            $errors['status'] = 'The status field must be 1, 2 or 3';
        }

        $location_type = $this->input->post('location_type');
        if ($location_type === null || $location_type === '') {
            $location_type = 'physical';
        }

        $content = $this->input->post('content');
        $name = $this->input->post('name');
        if ($status === 1) {
            if ($content === null || trim((string) $content) === '') {
                $errors['content'] = 'The content field is required';
            }
        } elseif ($content === null || trim((string) $content) === '') {
            $content = $name;
        }

        $date_start = $this->normalize_datetime($this->input->post('date_start'));
        $date_end = $this->normalize_datetime($this->input->post('date_end'));
        $all_day = (int) $this->input->post('all_day') ? 1 : 0;

        if ($all_day && $date_start) {
            $date_start = substr($date_start, 0, 10) . ' 00:00:00';
        }
        if ($all_day && $date_end) {
            $date_end = substr($date_end, 0, 10) . ' 23:59:59';
        }

        if ($status === 1 && !$date_start) {
            $errors['date_start'] = 'The date_start field is required';
        }
        if ($date_end && $date_start && strcmp($date_end, $date_start) < 0) {
            $errors['date_end'] = 'The date_end field must be greater than or equal to date_start';
        }

        $online_url = $this->input->post('online_url');
        $online_url = is_string($online_url) ? trim($online_url) : '';
        if ($location_type === 'online' || $location_type === 'hybrid') {
            if ($online_url === '') {
                $errors['online_url'] = 'The online_url field is required';
            } else {
                $online_url = prep_url($online_url);
                if (!$form->valid_url($online_url)) {
                    $errors['online_url'] = 'The online_url field must contain a valid URL';
                }
            }
        } elseif ($online_url === '') {
            $online_url = null;
        }

        $event = new EventModel();
        if ($is_update) {
            if (!$event->find($event_id)) {
                $this->response_error(lang('not_found_error'));
                return;
            }
        }

        $slug = trim((string) $this->input->post('slug'));
        if ($slug === '') {
            $slug = $event->ensure_slug($name, $is_update ? $event_id : null);
        } else {
            $slug = url_title(convert_accented_characters($slug), 'dash', true);
            if ($slug === '') {
                $slug = $event->ensure_slug($name, $is_update ? $event_id : null);
            } elseif ($event->slug_taken($slug, $is_update ? $event_id : null)) {
                $errors['slug'] = 'The slug field must contain a unique value.';
            }
        }

        if (!empty($errors)) {
            $this->response_error(lang('validations_error'), array('errors' => $errors), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $event->name = $name;
        $event->slug = $slug;
        $event->subtitle = $this->input->post('subtitle');
        $event->content = $content;
        $event->address = $this->input->post('address');
        $event->date_start = $date_start;
        $event->date_end = $date_end;
        $event->all_day = $all_day;
        $event->location_type = $location_type;
        $event->online_url = $online_url;
        $event->visibility = $this->input->post('visibility');
        $event->mainImage = $this->input->post('mainImage');
        $event->categorie_id = $this->input->post('categorie_id');
        $event->status = $status;
        $event->date_publish = $this->resolve_date_publish(
            $this->input->post('date_publish'),
            $is_update ? $event->date_publish : null,
            $status
        );

        if (!$is_update) {
            $event->user_id = userdata('user_id');
            $event->date_create = date("Y-m-d H:i:s");
        }

        if ($event->save()) {
            if (!$is_update && $event->slug === 'event') {
                $event->slug = $event->ensure_slug($name, $event->event_id);
                $event->save();
            }
            system_logger('events', $event->event_id, ($is_update ? 'updated' : 'created'), ($is_update ? 'An event has been updated' : 'An event has been created'));
            $this->response_ok($event);
            return;
        }

        $this->response_error(lang('unexpected_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    /**
     * PUT alias of POST update.
     *
     * @param mixed $id
     * @return void
     */
    public function index_put($id = null)
    {
        if ($id) {
            $_POST['event_id'] = $id;
        }
        $put = $this->put();
        if (is_array($put)) {
            foreach ($put as $key => $value) {
                $_POST[$key] = $value;
            }
        }
        $this->index_post();
    }

    /**
     * @param mixed $event_id
     * @return void
     */
    public function index_delete($event_id = null)
    {
        if (!$this->require_event_permision('DELETE_EVENT')) {
            return;
        }

        if ($event_id) {
            $event = new EventModel();
            $result = $event->find($event_id);
            if ($result) {
                $deleted = $event->delete();
                system_logger('events', $event->event_id, 'deleted', 'An event has been deleted');
                $this->response_ok(array("result" => $deleted));
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

    /**
     * Change status only. Body: { status: 1|2|3 }
     *
     * @param mixed $event_id
     * @return void
     */
    public function status_post($event_id = null)
    {
        if (!$this->require_event_permision('UPDATE_EVENT')) {
            return;
        }

        if ($event_id === null || $event_id === '' || $event_id === false) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $event = new EventModel();
        if (!$event->find($event_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $status = (int) $this->input->post('status');
        if (!in_array($status, array(1, 2, 3), true)) {
            $this->response_error(lang('validations_error'), array('errors' => array('status' => 'The status field must be 1, 2 or 3')), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $event->status = $status;
        if ($event->save()) {
            $token = ($status === 3) ? 'archived' : 'updated';
            $comment = ($status === 3) ? 'An event has been archived' : 'An event status has been updated';
            system_logger('events', $event->event_id, $token, $comment);
            $this->response_ok($event);
            return;
        }

        $this->response_error(lang('unexpected_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    /**
     * @param mixed $permision
     * @return bool
     */
    protected function require_event_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    protected function normalize_datetime($value)
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value . ' 00:00:00';
        }
        return $value;
    }

    /**
     * Persist posted date_publish. Empty + no previous + publishing → now(). Never overwrite unconditionally.
     *
     * @param mixed $posted
     * @param mixed $existing
     * @param int $status
     * @return mixed
     */
    protected function resolve_date_publish($posted, $existing, $status)
    {
        if ($posted !== null && $posted !== '' && $posted !== false) {
            return $posted;
        }
        if ($existing !== null && $existing !== '' && $existing !== '0000-00-00 00:00:00') {
            return $existing;
        }
        if ((int) $status === 1) {
            return date('Y-m-d H:i:s');
        }
        return $existing ? $existing : null;
    }

}
