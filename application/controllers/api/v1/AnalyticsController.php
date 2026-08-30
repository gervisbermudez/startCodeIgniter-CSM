<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Analytics API
 *
 * Authenticated GETs. Public POST /event and /conversion (no PII in responses).
 */
class AnalyticsController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');

        if ($this->is_public_tracking_post()) {
            if (!$this->validate_public_tracking_request()) {
                $this->response(array('code' => REST_Controller::HTTP_FORBIDDEN), REST_Controller::HTTP_FORBIDDEN);
                exit();
            }
        } elseif (!$this->verify_request()) {
            $this->response(array(
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ), REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
    }

    /**
     * REST _remap does not convert hyphens. Accept popular-pages and popular_pages.
     */
    public function _remap($object_called, $arguments = [])
    {
        $object_called = str_replace('-', '_', $object_called);
        parent::_remap($object_called, $arguments);
    }

    public function index_get()
    {
        $this->response_ok(array(
            'status' => 'ok',
            'message' => 'Analytics API is working',
            'version' => '2.1',
            'timestamp' => date('Y-m-d H:i:s')
        ));
    }

    public function overview_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');

        $stats = $this->analytics->get_overview_stats($start_date, $end_date, $page_id);

        $this->response_ok($stats);
    }

    public function trend_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');
        $days = $this->get('days');

        if ($start_date || $end_date) {
            $trend = $this->analytics->get_daily_trend($start_date, $end_date, $page_id);
        } else {
            $trend = $this->analytics->get_daily_trend($days ? $days : 30, null, $page_id);
        }

        $this->response_ok($trend ? $trend : array());
    }

    public function popular_pages_get()
    {
        $limit = $this->get('limit') ?: 10;
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');

        $pages = $this->analytics->get_popular_pages($limit, $start_date, $end_date, $page_id);

        $this->response_ok($pages ? $pages : array());
    }

    public function traffic_sources_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');

        $sources = $this->analytics->get_traffic_sources($start_date, $end_date, $page_id);

        $this->response_ok($sources ? $sources : array());
    }

    public function devices_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');

        $devices = $this->analytics->get_device_stats($start_date, $end_date, $page_id);

        $this->response_ok($devices ? $devices : array());
    }

    public function browsers_get()
    {
        $limit = $this->get('limit') ?: 10;
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');

        $browsers = $this->analytics->get_browser_stats($limit, $start_date, $end_date, $page_id);

        $this->response_ok($browsers ? $browsers : array());
    }

    public function geographic_get()
    {
        $limit = $this->get('limit') ?: 10;

        $locations = $this->analytics->get_geographic_stats($limit);

        $this->response_ok($locations ? $locations : array());
    }

    public function realtime_get()
    {
        $page_id = $this->get('page_id');
        $realtime = $this->analytics->get_realtime_visitors($page_id);

        $this->response_ok($realtime ? $realtime : array());
    }

    public function hourly_get()
    {
        $date = $this->get('date') ?: date('Y-m-d');
        $page_id = $this->get('page_id');

        $hourly = $this->analytics->get_hourly_distribution($date, $page_id);

        $this->response_ok($hourly ? $hourly : array());
    }

    public function events_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $limit = $this->get('limit') ?: 20;

        $events = $this->analytics->get_event_stats($start_date, $end_date, $limit);

        $this->response_ok($events ? $events : array());
    }

    public function funnel_post()
    {
        $pages = $this->post('pages');

        if (empty($pages) || !is_array($pages)) {
            $this->response_error('Invalid pages array');
            return;
        }

        $funnel = $this->analytics->get_conversion_funnel($pages);

        $this->response_ok($funnel);
    }

    public function export_get()
    {
        if (!function_exists('has_permisions') || !has_permisions('SELECT_ANALYTICS')) {
            $this->response(array(
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ), REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $filters = array(
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date'),
            'device_type' => $this->get('device_type'),
            'page_name' => $this->get('page_name'),
            'page_id' => $this->get('page_id'),
            'country_code' => $this->get('country_code'),
            'conversion' => $this->get('conversion'),
            'limit' => $this->get('limit') ?: 1000
        );

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $csv = $this->analytics->export_to_csv($filters);

        if ($csv === '') {
            $this->response_error('No data to export');
            return;
        }

        $filename = 'analytics_export_' . date('Y-m-d') . '.csv';
        $this->output
            ->set_status_header(200)
            ->set_content_type('text/csv', 'utf-8')
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_output($csv);
    }

    public function search_get()
    {
        $filters = array(
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date'),
            'device_type' => $this->get('device_type'),
            'page_name' => $this->get('page_name'),
            'page_id' => $this->get('page_id'),
            'country_code' => $this->get('country_code'),
            'conversion' => $this->get('conversion'),
            'limit' => $this->get('limit') ?: 100
        );

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $results = $this->analytics->search_with_filters($filters);

        $this->response_ok($results ? $results : array());
    }

    public function dashboard_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        $page_id = $this->get('page_id');

        $dashboard = array(
            'overview' => $this->analytics->get_overview_stats($start_date, $end_date, $page_id),
            'trend' => $this->analytics->get_daily_trend($start_date, $end_date, $page_id),
            'popular_pages' => $this->analytics->get_popular_pages(10, $start_date, $end_date, $page_id),
            'devices' => $this->analytics->get_device_stats($start_date, $end_date, $page_id),
            'browsers' => $this->analytics->get_browser_stats(10, $start_date, $end_date, $page_id),
            'traffic_sources' => $this->analytics->get_traffic_sources($start_date, $end_date, $page_id),
            'events' => $this->analytics->get_event_stats($start_date, $end_date),
            'realtime' => $this->analytics->get_realtime_visitors($page_id),
            'hourly' => $this->analytics->get_hourly_distribution(date('Y-m-d'), $page_id)
        );

        $this->response_ok($dashboard);
    }

    public function event_post()
    {
        $payload = $this->read_tracking_payload();
        $category = isset($payload['category']) ? $payload['category'] : null;
        $action = isset($payload['action']) ? $payload['action'] : null;
        $label = isset($payload['label']) ? $payload['label'] : null;
        $value = isset($payload['value']) ? $payload['value'] : null;
        $metadata = isset($payload['metadata']) ? $payload['metadata'] : null;

        if (empty($category) || empty($action)) {
            $this->response_error('Category and action are required');
            return;
        }

        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        $result = $this->tracker->track_event($category, $action, $label, $value, $metadata);

        if ($result) {
            $this->response_ok(array('message' => 'Event tracked'));
        } else {
            $this->response_ok(array('message' => 'Event ignored'));
        }
    }

    public function conversion_post()
    {
        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        $this->tracker->track_conversion();
        $this->response_ok(array('message' => 'Conversion tracked'));
    }

    private function is_public_tracking_post()
    {
        $method = strtolower($this->input->method(true));
        if ($method !== 'post') {
            return false;
        }

        $uri = strtolower(str_replace('-', '_', $this->uri->uri_string()));
        return (bool) preg_match('#analytics/(event|conversion)/?$#', $uri);
    }

    /**
     * Same-origin (or missing Origin) + simple per-IP rate limit.
     */
    private function validate_public_tracking_request()
    {
        $allowed_host = parse_url(base_url(), PHP_URL_HOST);
        $origin = $this->input->get_request_header('Origin', true);
        if ($origin) {
            $origin_host = parse_url($origin, PHP_URL_HOST);
            if ($origin_host && strcasecmp($origin_host, $allowed_host) !== 0) {
                return false;
            }
        }

        $referer = $this->input->get_request_header('Referer', true);
        if (!$origin && $referer) {
            $referer_host = parse_url($referer, PHP_URL_HOST);
            if ($referer_host && strcasecmp($referer_host, $allowed_host) !== 0) {
                return false;
            }
        }

        $ip = $this->input->ip_address();
        $xff = $this->input->server('HTTP_X_FORWARDED_FOR');
        if ($xff) {
            $parts = explode(',', $xff);
            $ip = trim($parts[0]);
        }
        $cache_key = 'analytics_rl_' . md5($ip);
        $hits = get_cached($cache_key, 0);
        if ($hits >= 300) {
            return false;
        }
        set_cached($cache_key, (int) $hits + 1, 60);

        return true;
    }

    /**
     * JSON body, including sendBeacon text/plain via php://input.
     */
    private function read_tracking_payload()
    {
        $payload = array(
            'category' => $this->post('category'),
            'action' => $this->post('action'),
            'label' => $this->post('label'),
            'value' => $this->post('value'),
            'metadata' => $this->post('metadata'),
        );

        if (!empty($payload['category']) && !empty($payload['action'])) {
            return $payload;
        }

        $raw = $this->input->raw_input_stream;
        if (!$raw) {
            $raw = @file_get_contents('php://input');
        }
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach ($payload as $key => $value) {
                    if (($value === null || $value === '') && isset($decoded[$key])) {
                        $payload[$key] = $decoded[$key];
                    }
                }
            }
        }

        return $payload;
    }
}
