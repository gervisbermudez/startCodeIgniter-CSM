<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class CalendarController extends REST_Controller
{
    const MAX_SPAN_DAYS = 400;

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
     * GET /api/v1/calendar?from=&to=&types=events
     *
     * @return void
     */
    public function index_get()
    {
        if (!$this->require_calendar_permision('SELECT_CALENDAR')) {
            return;
        }

        $from = $this->parse_bound($this->get('from'), false);
        $to = $this->parse_bound($this->get('to'), false);

        if ($from === null && $to === null) {
            $from = date('Y-m-01 00:00:00');
            $to = date('Y-m-d H:i:s', strtotime($from . ' +1 month'));
        } elseif ($from === null || $to === null) {
            $this->response_error(lang('validations_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $from_ts = strtotime($from);
        $to_ts = strtotime($to);
        if ($from_ts === false || $to_ts === false || $from_ts >= $to_ts) {
            $this->response_error(lang('validations_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $max_to = $from_ts + (self::MAX_SPAN_DAYS * 86400);
        if ($to_ts > $max_to) {
            $to = date('Y-m-d H:i:s', $max_to);
        }

        $wanted = $this->parse_types($this->get('types'));
        $items = array();

        if (in_array('events', $wanted, true) && $this->has_permision('SELECT_EVENTS')) {
            $event = new EventModel();
            $rows = $event->calendar_range($from, $to);
            foreach ($rows as $row) {
                $items[] = $this->map_event($row);
            }
        }

        $this->response_ok(array(
            'from' => $from,
            'to' => $to,
            'items' => $items,
        ));
    }

    public function index_post()
    {
        $this->response(array(), REST_Controller::HTTP_NOT_FOUND);
    }

    public function index_put($id = null)
    {
        $this->response(array(), REST_Controller::HTTP_NOT_FOUND);
    }

    public function index_delete($id = null)
    {
        $this->response(array(), REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * @param mixed $permision
     * @return bool
     */
    protected function require_calendar_permision($permision)
    {
        if (!$this->has_permision($permision)) {
            $this->response_error(
                lang('not_have_permissions'),
                array(),
                REST_Controller::HTTP_FORBIDDEN,
                REST_Controller::HTTP_FORBIDDEN
            );
            return false;
        }
        return true;
    }

    /**
     * @param string $permision
     * @return bool
     */
    protected function has_permision($permision)
    {
        return function_exists('has_permisions') && has_permisions($permision);
    }

    /**
     * types omitted → events. Empty or "none" → no types.
     *
     * @param mixed $raw
     * @return array
     */
    protected function parse_types($raw)
    {
        if ($raw === null) {
            return array('events');
        }
        $raw = strtolower(trim((string) $raw));
        if ($raw === '' || $raw === 'none') {
            return array();
        }
        $wanted = array();
        $parts = explode(',', $raw);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === 'events') {
                $wanted[$part] = $part;
            }
        }
        return array_values($wanted);
    }

    /**
     * @param mixed $value
     * @param bool $end_of_day unused; from/to are exclusive-end from FullCalendar
     * @return string|null
     */
    protected function parse_bound($value, $end_of_day)
    {
        if ($value === null || $value === false) {
            return null;
        }
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value . ($end_of_day ? ' 23:59:59' : ' 00:00:00');
        }
        $value = str_replace('T', ' ', $value);
        if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2})(:\d{2})?/', $value, $m)) {
            $base = $m[1];
            $sec = isset($m[2]) && $m[2] !== '' ? $m[2] : ':00';
            return $base . $sec;
        }
        return null;
    }

    /**
     * @param object $row
     * @return array
     */
    protected function map_event($row)
    {
        $event_id = isset($row->event_id) ? $row->event_id : 0;
        $all_day = isset($row->all_day) && ((int) $row->all_day === 1);
        $start = isset($row->date_start) ? $row->date_start : null;
        $end = isset($row->date_end) && $row->date_end ? $row->date_end : $start;
        $status = isset($row->status) ? (int) $row->status : 0;
        $visibility = isset($row->visibility) ? (int) $row->visibility : 0;
        $slug = isset($row->slug) ? trim((string) $row->slug) : '';
        $location_type = isset($row->location_type) ? $row->location_type : 'physical';
        $place = isset($row->address) ? $row->address : '';
        if (($location_type === 'online' || $location_type === 'hybrid') && !empty($row->online_url)) {
            $place = $row->online_url;
        }

        $public_url = null;
        if ($status === 1 && $visibility === 1 && $slug !== '') {
            $public_url = base_url('events/' . $slug);
        }

        return array(
            'id' => 'event_' . $event_id,
            'type' => 'events',
            'title' => isset($row->name) ? $row->name : '',
            'start' => $start,
            'end' => $end,
            'allDay' => $all_day,
            'status' => $status,
            'visibility' => $visibility,
            'place' => $place ? $place : null,
            'editUrl' => base_url('admin/events/edit/' . $event_id),
            'publicUrl' => $public_url,
        );
    }
}
