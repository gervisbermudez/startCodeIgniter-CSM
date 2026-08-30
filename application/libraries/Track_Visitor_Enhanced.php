<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * First-party visitor tracking.
 * Switch: config('SITEM_TRACK_VISITORS') == 'Si'  (not CI config_item('enable_tracking'))
 *
 * @version 2.1
 */
class Track_Visitor_Enhanced
{
    private $ci;

    private $IGNORE_SEARCH_BOTS = true;
    private $HONOR_DO_NOT_TRACK = false;
    private $CONTROLLER_IGNORE_LIST = array('admin', 'api');
    private $IP_IGNORE_LIST = array('127.0.0.1', '::1');
    private $SESSION_TIMEOUT = 1800;

    private $table_tracking = "user_tracking";
    private $table_sessions = "user_sessions";
    private $table_events = "user_tracking_events";

    private $session_id;
    private $current_page_entry_time;
    private $has_page_id_column = null;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('user_agent');
        $this->ci->load->helper('cookie');
    }

    /**
     * First-party tracking master switch (site_config).
     */
    public function is_tracking_enabled()
    {
        return config('SITEM_TRACK_VISITORS') == 'Si';
    }

    /**
     * Client IP: CF-Connecting-IP, then first public hop of X-Forwarded-For, then REMOTE_ADDR.
     */
    public function get_client_ip()
    {
        $candidates = array();

        $cf = $this->ci->input->server('HTTP_CF_CONNECTING_IP');
        if ($cf) {
            $candidates[] = trim($cf);
        }

        $xff = $this->ci->input->server('HTTP_X_FORWARDED_FOR');
        if ($xff) {
            $parts = explode(',', $xff);
            foreach ($parts as $part) {
                $candidates[] = trim($part);
            }
        }

        $remote = $this->ci->input->server('REMOTE_ADDR');
        if ($remote) {
            $candidates[] = $remote;
        }

        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }

        return $remote ? $remote : '0.0.0.0';
    }

    /**
     * Lazy session: only when we actually track.
     */
    public function ensure_session()
    {
        if ($this->session_id) {
            return $this->session_id;
        }

        $cookie_session = get_cookie('tracking_session_id');
        if (!$cookie_session) {
            $cookie_session = $this->ci->session->userdata('tracking_session_id');
        }

        if ($cookie_session && $this->is_session_active($cookie_session)) {
            $this->session_id = $cookie_session;
            $this->touch_session_activity();
        } else {
            $this->create_new_session();
        }

        $this->current_page_entry_time = time();
        $this->ci->session->set_userdata('page_entry_time', $this->current_page_entry_time);

        return $this->session_id;
    }

    private function create_new_session()
    {
        $this->session_id = $this->generate_session_id();

        $device_info = $this->detect_device();
        $referer = $this->ci->agent->referrer();
        $campaign_data = $this->parse_campaign_params();
        $is_new = !get_cookie('returning_visitor');

        $session_data = array(
            'session_id' => $this->session_id,
            'first_visit' => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s'),
            'total_pages' => 0,
            'total_time' => 0,
            'is_new_visitor' => $is_new ? 1 : 0,
            'client_ip' => $this->get_client_ip(),
            'user_agent' => $this->ci->agent->agent_string(),
            'browser' => $device_info['browser'],
            'platform' => $device_info['platform'],
            'device_type' => $device_info['device_type'],
            'entry_page' => $this->get_visible_path(),
            'referer_source' => $referer,
            'campaign_source' => $campaign_data['source'],
            'campaign_medium' => $campaign_data['medium'],
            'campaign_name' => $campaign_data['name'],
            'status' => 1
        );

        $this->ci->db->insert($this->table_sessions, $session_data);

        $cookie_expiry = 60 * 60 * 24 * 30;
        set_cookie('tracking_session_id', $this->session_id, $cookie_expiry);
        set_cookie('returning_visitor', '1', $cookie_expiry);
        $this->ci->session->set_userdata('tracking_session_id', $this->session_id);
    }

    private function generate_session_id()
    {
        return uniqid('sess_', true) . '_' . bin2hex(random_bytes(8));
    }

    private function is_session_active($session_id)
    {
        $query = $this->ci->db->select('last_activity')
            ->from($this->table_sessions)
            ->where('session_id', $session_id)
            ->get();

        if ($query->num_rows() > 0) {
            $session = $query->row();
            $last_activity = strtotime($session->last_activity);
            if ((time() - $last_activity) < $this->SESSION_TIMEOUT) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update last_activity without incrementing page count.
     */
    private function touch_session_activity()
    {
        $this->ci->db->where('session_id', $this->session_id);
        $this->ci->db->set('last_activity', 'NOW()', false);
        $this->ci->db->update($this->table_sessions);
    }

    /**
     * Close the previous hit (time on page, not an exit anymore) then bump session pages.
     */
    private function finalize_previous_hit()
    {
        $previous_entry_time = $this->ci->session->userdata('page_entry_time');
        $previous_tracking_id = $this->ci->session->userdata('current_tracking_id');

        if ($previous_tracking_id) {
            $update = array(
                'exit_page' => 0,
                'is_bounce' => 0
            );
            if ($previous_entry_time) {
                $update['time_on_page'] = max(0, time() - (int) $previous_entry_time);
            }
            $this->ci->db->where('user_tracking_id', $previous_tracking_id);
            $this->ci->db->update($this->table_tracking, $update);

            $this->ci->db->where('session_id', $this->session_id);
            $this->ci->db->update($this->table_tracking, array('is_bounce' => 0));
        }
    }

    public function visitor_track()
    {
        if ($this->should_track_pageview() !== true) {
            return;
        }

        $this->ensure_session();
        $this->log_visitor();
    }

    /**
     * Pageview gate. First-party switch is SITEM_TRACK_VISITORS, not enable_tracking.
     */
    private function should_track_pageview()
    {
        if (!$this->is_tracking_enabled()) {
            return false;
        }

        if ($this->IGNORE_SEARCH_BOTS && $this->is_search_bot()) {
            return false;
        }

        if ($this->HONOR_DO_NOT_TRACK && !$this->allow_tracking()) {
            return false;
        }

        if ($this->is_ignored_admin_or_api()) {
            return false;
        }

        $ip = $this->get_client_ip();
        if (in_array($ip, $this->IP_IGNORE_LIST, true)) {
            return false;
        }

        if ($this->ci->session->userdata('logged_in')) {
            return false;
        }

        return true;
    }

    /**
     * Events from the public JS client (URI is /api/...). Do not apply CONTROLLER_IGNORE_LIST.
     */
    private function should_track_event()
    {
        if (!$this->is_tracking_enabled()) {
            return false;
        }

        if ($this->IGNORE_SEARCH_BOTS && $this->is_search_bot()) {
            return false;
        }

        if ($this->ci->session->userdata('logged_in')) {
            return false;
        }

        $ip = $this->get_client_ip();
        if (in_array($ip, $this->IP_IGNORE_LIST, true)) {
            return false;
        }

        return true;
    }

    /**
     * Case-insensitive class name and first URI segment vs admin/api.
     */
    private function is_ignored_admin_or_api()
    {
        $class = strtolower((string) $this->ci->router->fetch_class());
        $segment = strtolower((string) $this->ci->uri->segment(1));

        foreach ($this->CONTROLLER_IGNORE_LIST as $ignored) {
            $ignored = strtolower($ignored);
            if ($segment === $ignored) {
                return true;
            }
            if ($class === $ignored || strpos($class, $ignored) !== false) {
                return true;
            }
        }

        return false;
    }

    private function log_visitor()
    {
        $current_page = $this->get_visible_path();
        $previous_page = $this->ci->session->userdata('current_tracked_page');

        if ($previous_page === $current_page) {
            return;
        }

        if ($previous_page) {
            $this->finalize_previous_hit();
        }

        $device_info = $this->detect_device();
        $page_info = $this->get_page_info();
        $page_id = $this->resolve_page_id($page_info['page_name']);

        $this->ci->db->where('session_id', $this->session_id);
        $this->ci->db->set('last_activity', 'NOW()', false);
        $this->ci->db->set('total_pages', 'total_pages + 1', false);
        $this->ci->db->set('exit_page', $current_page);
        $this->ci->db->update($this->table_sessions);

        $session_pages = $this->get_session_page_count();
        $is_bounce = ($session_pages <= 1) ? 1 : 0;

        $tracking_data = array(
            'session_id' => $this->session_id,
            'client_ip' => $this->get_client_ip(),
            'user_agent' => $this->ci->agent->agent_string(),
            'browser' => $device_info['browser'],
            'browser_version' => $device_info['browser_version'],
            'platform' => $device_info['platform'],
            'device_type' => $device_info['device_type'],
            'screen_resolution' => $this->ci->input->get('screen_res'),
            'language' => $this->ci->input->server('HTTP_ACCEPT_LANGUAGE'),
            'requested_url' => $page_info['page_name'],
            'referer_page' => $this->ci->agent->referrer(),
            'page_name' => $page_info['page_name'],
            'query_string' => $page_info['query_string'],
            'time_on_page' => 0,
            'is_bounce' => $is_bounce,
            'exit_page' => 1,
            'conversion' => 0,
            'status' => 1
        );

        if ($this->has_page_id_column() && $page_id) {
            $tracking_data['page_id'] = $page_id;
        }

        $this->ci->db->insert($this->table_tracking, $tracking_data);
        $tracking_id = $this->ci->db->insert_id();

        $this->ci->session->set_userdata('current_tracking_id', $tracking_id);
        $this->ci->session->set_userdata('current_tracked_page', $current_page);
        $this->ci->session->set_userdata('page_entry_time', time());
        $this->ci->session->set_userdata('user_tracking_id', $tracking_id);
    }

    public function track_event($category, $action, $label = null, $value = null, $metadata = null)
    {
        if (!$this->should_track_event()) {
            return false;
        }

        $this->ensure_session();

        if (!$this->session_id) {
            return false;
        }

        $event_data = array(
            'session_id' => $this->session_id,
            'user_tracking_id' => $this->ci->session->userdata('current_tracking_id'),
            'event_category' => $category,
            'event_action' => $action,
            'event_label' => $label,
            'event_value' => $value,
            'page_url' => current_url(),
            'metadata' => $metadata ? json_encode($metadata) : null
        );

        return $this->ci->db->insert($this->table_events, $event_data);
    }

    public function track_conversion()
    {
        if (!$this->should_track_event()) {
            return false;
        }

        $this->ensure_session();

        $tracking_id = $this->ci->session->userdata('current_tracking_id');

        if ($this->session_id) {
            $this->ci->db->where('session_id', $this->session_id);
            $this->ci->db->update($this->table_sessions, array('converted' => 1));
        }

        if ($tracking_id) {
            $this->ci->db->where('user_tracking_id', $tracking_id);
            $this->ci->db->update($this->table_tracking, array('conversion' => 1));
        }

        $this->track_event('Conversion', 'Form Submit', 'Contact Form');

        return true;
    }

    private function detect_device()
    {
        $device_info = array(
            'browser' => $this->ci->agent->browser(),
            'browser_version' => $this->ci->agent->version(),
            'platform' => $this->ci->agent->platform(),
            'device_type' => 'desktop'
        );

        if ($this->ci->agent->is_mobile()) {
            $device_info['device_type'] = 'mobile';
        } elseif ($this->is_tablet()) {
            $device_info['device_type'] = 'tablet';
        } elseif ($this->ci->agent->is_robot()) {
            $device_info['device_type'] = 'bot';
        }

        return $device_info;
    }

    private function is_tablet()
    {
        $user_agent = $this->ci->agent->agent_string();
        $tablet_keywords = array('ipad', 'tablet', 'kindle', 'playbook', 'nexus 7', 'nexus 10');

        foreach ($tablet_keywords as $keyword) {
            if (stripos($user_agent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Visible path/slug — not "PageController/home".
     */
    private function get_visible_path()
    {
        $uri = $this->ci->input->server('REQUEST_URI');
        $path = parse_url($uri, PHP_URL_PATH);
        if ($path === null || $path === false) {
            $path = '/' . $this->ci->uri->uri_string();
        }
        $path = preg_replace('#/index\.php(/|$)#', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        if ($path !== '/' && preg_match('#/index$#', $path)) {
            $path = preg_replace('#/index$#', '', $path);
        }
        if ($path === '' || $path === false) {
            $path = '/';
        }
        return $path;
    }

    private function get_page_info()
    {
        $path = $this->get_visible_path();
        $query = $this->ci->input->server('QUERY_STRING');

        return array(
            'page_name' => $path,
            'query_string' => $query ? $query : ''
        );
    }

    private function resolve_page_id($path)
    {
        $trimmed = trim($path, '/');
        $candidates = array($path, $trimmed, '/' . $trimmed);

        $home_id = config('SITE_HOME_PAGE_ID');
        if ($path === '/' || $trimmed === '') {
            if ($home_id) {
                return (int) $home_id;
            }
            $home_query = $this->ci->db->query(
                "SELECT page_id FROM page WHERE status = 1 AND (path = '' OR path = '/' OR path IS NULL) LIMIT 1"
            );
            if ($home_query && $home_query->num_rows() > 0) {
                return (int) $home_query->row()->page_id;
            }
            return null;
        }

        $usable = array();
        foreach ($candidates as $candidate) {
            if ($candidate === '' || $candidate === '/') {
                continue;
            }
            $usable[] = $candidate;
        }
        if (empty($usable)) {
            return null;
        }

        $this->ci->db->select('page_id');
        $this->ci->db->from('page');
        $this->ci->db->where('status', 1);
        $this->ci->db->group_start();
        foreach ($usable as $candidate) {
            $this->ci->db->or_where('path', $candidate);
        }
        $this->ci->db->group_end();
        $this->ci->db->limit(1);
        $query = $this->ci->db->get();

        if ($query->num_rows() > 0) {
            return (int) $query->row()->page_id;
        }

        return null;
    }

    private function has_page_id_column()
    {
        if ($this->has_page_id_column === null) {
            $this->has_page_id_column = $this->ci->db->field_exists('page_id', $this->table_tracking);
        }
        return $this->has_page_id_column;
    }

    private function get_session_page_count()
    {
        $query = $this->ci->db->select('total_pages')
            ->from($this->table_sessions)
            ->where('session_id', $this->session_id)
            ->get();

        if ($query->num_rows() > 0) {
            return (int) $query->row()->total_pages;
        }

        return 0;
    }

    private function parse_campaign_params()
    {
        return array(
            'source' => $this->ci->input->get('utm_source'),
            'medium' => $this->ci->input->get('utm_medium'),
            'name' => $this->ci->input->get('utm_campaign')
        );
    }

    private function allow_tracking()
    {
        $dnt = $this->ci->input->server('HTTP_DNT');
        return ($dnt != 1);
    }

    private function is_search_bot()
    {
        return $this->ci->agent->is_robot();
    }

    public function get_session_id()
    {
        return $this->session_id;
    }

    public function clean_old_sessions($days = 30)
    {
        $date_limit = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $this->ci->db->where('last_activity <', $date_limit);
        $this->ci->db->delete($this->table_sessions);

        return $this->ci->db->affected_rows();
    }
}
