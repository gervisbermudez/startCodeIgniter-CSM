<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Enhanced Visitor Tracking Library
 * 
 * Improved version with session tracking, device detection, 
 * event tracking, and analytics capabilities
 * 
 * @author Enhanced by AI Assistant
 * @version 2.0
 */
class Track_Visitor_Enhanced
{
    private $ci;
    
    /**
     * Configuration
     */
    private $IGNORE_SEARCH_BOTS = true;
    private $HONOR_DO_NOT_TRACK = false;
    private $CONTROLLER_IGNORE_LIST = array('admin', 'api');
    private $IP_IGNORE_LIST = array('127.0.0.1');
    private $SESSION_TIMEOUT = 1800; // 30 minutes in seconds
    
    /**
     * Tables
     */
    private $table_tracking = "user_tracking";
    private $table_sessions = "user_sessions";
    private $table_events = "user_tracking_events";
    
    /**
     * Session data
     */
    private $session_id;
    private $current_page_entry_time;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('user_agent');
        $this->ci->load->helper('cookie');
        
        // Initialize or retrieve session ID
        $this->init_session();
    }

    /**
     * Initialize tracking session
     */
    private function init_session()
    {
        // Try to get session from cookie first
        $cookie_session = get_cookie('tracking_session_id');
        
        if ($cookie_session) {
            // Verify session is still active
            if ($this->is_session_active($cookie_session)) {
                $this->session_id = $cookie_session;
                $this->update_session_activity();
            } else {
                $this->create_new_session();
            }
        } else {
            $this->create_new_session();
        }
        
        // Store page entry time for time-on-page calculation
        $this->current_page_entry_time = time();
        $this->ci->session->set_userdata('page_entry_time', $this->current_page_entry_time);
    }

    /**
     * Create new tracking session
     */
    private function create_new_session()
    {
        $this->session_id = $this->generate_session_id();
        
        // Get device and browser info
        $device_info = $this->detect_device();
        
        // Get referrer info
        $referer = $this->ci->agent->referrer();
        $campaign_data = $this->parse_campaign_params();
        
        // Check if new or returning visitor
        $is_new = !get_cookie('returning_visitor');
        
        // Create session record
        $session_data = array(
            'session_id' => $this->session_id,
            'first_visit' => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s'),
            'total_pages' => 1,
            'total_time' => 0,
            'is_new_visitor' => $is_new ? 1 : 0,
            'client_ip' => $this->ci->input->server('REMOTE_ADDR'),
            'user_agent' => $this->ci->agent->agent_string(),
            'browser' => $device_info['browser'],
            'platform' => $device_info['platform'],
            'device_type' => $device_info['device_type'],
            'entry_page' => current_url(),
            'referer_source' => $referer,
            'campaign_source' => $campaign_data['source'],
            'campaign_medium' => $campaign_data['medium'],
            'campaign_name' => $campaign_data['name'],
            'status' => 1
        );
        
        $this->ci->db->insert($this->table_sessions, $session_data);
        
        // Set cookies (expire in 30 days)
        $cookie_expiry = 60 * 60 * 24 * 30;
        set_cookie('tracking_session_id', $this->session_id, $cookie_expiry);
        set_cookie('returning_visitor', '1', $cookie_expiry);
        
        // Store in PHP session
        $this->ci->session->set_userdata('tracking_session_id', $this->session_id);
    }

    /**
     * Generate unique session ID
     */
    private function generate_session_id()
    {
        return uniqid('sess_', true) . '_' . bin2hex(random_bytes(8));
    }

    /**
     * Check if session is still active
     */
    private function is_session_active($session_id)
    {
        $query = $this->ci->db->select('last_activity')
            ->from($this->table_sessions)
            ->where('session_id', $session_id)
            ->get();
        
        if ($query->num_rows() > 0) {
            $session = $query->row();
            $last_activity = strtotime($session->last_activity);
            $current_time = time();
            
            // Check if session expired (default 30 minutes)
            if (($current_time - $last_activity) < $this->SESSION_TIMEOUT) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Update session activity
     */
    private function update_session_activity()
    {
        // Calculate time on previous page if exists
        $previous_entry_time = $this->ci->session->userdata('page_entry_time');
        if ($previous_entry_time) {
            $time_on_page = time() - $previous_entry_time;
            
            // Update previous page record with time spent
            $previous_tracking_id = $this->ci->session->userdata('current_tracking_id');
            if ($previous_tracking_id) {
                $this->ci->db->where('user_tracking_id', $previous_tracking_id);
                $this->ci->db->update($this->table_tracking, array(
                    'time_on_page' => $time_on_page
                ));
            }
        }
        
        // Update session
        $this->ci->db->where('session_id', $this->session_id);
        $this->ci->db->set('last_activity', 'NOW()', false);
        $this->ci->db->set('total_pages', 'total_pages + 1', false);
        $this->ci->db->update($this->table_sessions);
    }

    /**
     * Main tracking method
     */
    public function visitor_track()
    {
        $proceed = $this->should_track();
        
        if ($proceed === true) {
            $this->log_visitor();
        }
    }

    /**
     * Check if we should track this visit
     */
    private function should_track()
    {
        // Check if tracking is enabled
        if (!config_item('enable_tracking')) {
            return false;
        }
        
        // Check for bots
        if ($this->IGNORE_SEARCH_BOTS && $this->is_search_bot()) {
            return false;
        }
        
        // Honor Do Not Track
        if ($this->HONOR_DO_NOT_TRACK && !$this->allow_tracking()) {
            return false;
        }
        
        // Check ignored controllers
        foreach ($this->CONTROLLER_IGNORE_LIST as $controller) {
            if (strpos(trim($this->ci->router->fetch_class()), $controller) !== false) {
                return false;
            }
        }
        
        // Check ignored IPs
        if (in_array($this->ci->input->server('REMOTE_ADDR'), $this->IP_IGNORE_LIST)) {
            return false;
        }
        
        // Don't track logged in users (admin)
        if ($this->ci->session->userdata('logged_in')) {
            return false;
        }
        
        return true;
    }

    /**
     * Log visitor page view
     */
    private function log_visitor()
    {
        $current_page = current_url();
        $previous_page = $this->ci->session->userdata('current_tracked_page');
        
        // Only track if it's a different page (avoid refresh tracking)
        if ($previous_page === $current_page) {
            return;
        }
        
        $device_info = $this->detect_device();
        $page_info = $this->get_page_info();
        
        // Check if this is a bounce (only 1 page in session)
        $is_bounce = $this->check_if_bounce();
        
        $tracking_data = array(
            'session_id' => $this->session_id,
            'client_ip' => $this->ci->input->server('REMOTE_ADDR'),
            'user_agent' => $this->ci->agent->agent_string(),
            'browser' => $device_info['browser'],
            'browser_version' => $device_info['browser_version'],
            'platform' => $device_info['platform'],
            'device_type' => $device_info['device_type'],
            'screen_resolution' => $this->ci->input->get('screen_res'), // From JS
            'language' => $this->ci->input->server('HTTP_ACCEPT_LANGUAGE'),
            'requested_url' => $this->ci->input->server('REQUEST_URI'),
            'referer_page' => $this->ci->agent->referrer(),
            'page_name' => $page_info['page_name'],
            'query_string' => $page_info['query_string'],
            'time_on_page' => 0, // Will be updated when user navigates away
            'is_bounce' => $is_bounce ? 1 : 0,
            'exit_page' => 0, // Will be updated if user leaves
            'conversion' => 0,
            'status' => 1
        );
        
        $this->ci->db->insert($this->table_tracking, $tracking_data);
        $tracking_id = $this->ci->db->insert_id();
        
        // Store current tracking ID and page
        $this->ci->session->set_userdata('current_tracking_id', $tracking_id);
        $this->ci->session->set_userdata('current_tracked_page', $current_page);
    }

    /**
     * Track custom event
     */
    public function track_event($category, $action, $label = null, $value = null, $metadata = null)
    {
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

    /**
     * Track conversion
     */
    public function track_conversion()
    {
        $tracking_id = $this->ci->session->userdata('current_tracking_id');
        
        if ($tracking_id) {
            // Update current page as conversion
            $this->ci->db->where('user_tracking_id', $tracking_id);
            $this->ci->db->update($this->table_tracking, array('conversion' => 1));
            
            // Update session
            $this->ci->db->where('session_id', $this->session_id);
            $this->ci->db->update($this->table_sessions, array('converted' => 1));
            
            // Track as event
            $this->track_event('Conversion', 'Form Submit', 'Contact Form');
            
            return true;
        }
        
        return false;
    }

    /**
     * Detect device information
     */
    private function detect_device()
    {
        $device_info = array(
            'browser' => $this->ci->agent->browser(),
            'browser_version' => $this->ci->agent->version(),
            'platform' => $this->ci->agent->platform(),
            'device_type' => 'desktop'
        );
        
        // Detect device type
        if ($this->ci->agent->is_mobile()) {
            $device_info['device_type'] = 'mobile';
        } elseif ($this->is_tablet()) {
            $device_info['device_type'] = 'tablet';
        } elseif ($this->ci->agent->is_robot()) {
            $device_info['device_type'] = 'bot';
        }
        
        return $device_info;
    }

    /**
     * Check if device is tablet
     */
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
     * Get page information
     */
    private function get_page_info()
    {
        $page_name = $this->ci->router->fetch_class() . '/' . $this->ci->router->fetch_method();
        $page_length = strlen(trim($page_name));
        $query_params = trim(substr($this->ci->uri->uri_string(), $page_length + 1));
        
        return array(
            'page_name' => $page_name,
            'query_string' => strlen($query_params) ? $query_params : ''
        );
    }

    /**
     * Parse campaign parameters (UTM)
     */
    private function parse_campaign_params()
    {
        return array(
            'source' => $this->ci->input->get('utm_source'),
            'medium' => $this->ci->input->get('utm_medium'),
            'name' => $this->ci->input->get('utm_campaign')
        );
    }

    /**
     * Check if this is a bounce
     */
    private function check_if_bounce()
    {
        $query = $this->ci->db->select('total_pages')
            ->from($this->table_sessions)
            ->where('session_id', $this->session_id)
            ->get();
        
        if ($query->num_rows() > 0) {
            $session = $query->row();
            return ($session->total_pages <= 1);
        }
        
        return true;
    }

    /**
     * Check Do Not Track header
     */
    private function allow_tracking()
    {
        $dnt = $this->ci->input->server('HTTP_DNT');
        return ($dnt != 1);
    }

    /**
     * Check if visitor is a search bot
     */
    private function is_search_bot()
    {
        return $this->ci->agent->is_robot();
    }

    /**
     * Get current session ID
     */
    public function get_session_id()
    {
        return $this->session_id;
    }

    /**
     * Clean old sessions (run via cron)
     */
    public function clean_old_sessions($days = 30)
    {
        $date_limit = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        // Delete old sessions
        $this->ci->db->where('last_activity <', $date_limit);
        $this->ci->db->delete($this->table_sessions);
        
        return $this->ci->db->affected_rows();
    }
}
