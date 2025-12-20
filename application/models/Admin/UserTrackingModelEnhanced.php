<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Enhanced User Tracking Model with Analytics
 * 
 * Provides comprehensive analytics methods for visitor tracking data
 * 
 * @version 2.0
 */
class UserTrackingModelEnhanced extends MY_Model
{
    public $table = 'user_tracking';
    public $primaryKey = 'user_tracking_id';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get overview statistics for a date range
     * 
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_overview_stats($start_date = null, $end_date = null)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }

        // Get basic stats
        $this->db->select('
            COUNT(DISTINCT session_id) as total_sessions,
            COUNT(*) as total_pageviews,
            COUNT(DISTINCT client_ip) as unique_visitors,
            AVG(time_on_page) as avg_time_on_page,
            SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as total_bounces,
            SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as total_conversions,
            SUM(CASE WHEN device_type = "mobile" THEN 1 ELSE 0 END) as mobile_visits,
            SUM(CASE WHEN device_type = "desktop" THEN 1 ELSE 0 END) as desktop_visits,
            SUM(CASE WHEN device_type = "tablet" THEN 1 ELSE 0 END) as tablet_visits
        ');
        $this->db->where('DATE(date_create) >=', $start_date);
        $this->db->where('DATE(date_create) <=', $end_date);
        $this->db->where('status', 1);
        $query = $this->db->get($this->table);
        
        $stats = $query->row_array();
        
        // Calculate rates
        $stats['bounce_rate'] = $stats['total_sessions'] > 0 
            ? round(($stats['total_bounces'] / $stats['total_sessions']) * 100, 2) 
            : 0;
        
        $stats['conversion_rate'] = $stats['total_sessions'] > 0 
            ? round(($stats['total_conversions'] / $stats['total_sessions']) * 100, 2) 
            : 0;
        
        $stats['pages_per_session'] = $stats['total_sessions'] > 0 
            ? round($stats['total_pageviews'] / $stats['total_sessions'], 2) 
            : 0;

        return $stats;
    }

    /**
     * Get daily statistics trend
     * 
     * @param int $days Number of days to retrieve
     * @return array
     */
    public function get_daily_trend($days = 30)
    {
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        $this->db->select('
            DATE(date_create) as date,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews,
            COUNT(DISTINCT client_ip) as unique_visitors,
            AVG(time_on_page) as avg_time,
            SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as conversions
        ');
        $this->db->where('DATE(date_create) >=', $start_date);
        $this->db->where('status', 1);
        $this->db->group_by('DATE(date_create)');
        $this->db->order_by('date', 'ASC');
        
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get most popular pages
     * 
     * @param int $limit
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_popular_pages($limit = 10, $start_date = null, $end_date = null)
    {
        $this->db->select('
            page_name,
            COUNT(*) as visits,
            COUNT(DISTINCT session_id) as unique_visits,
            AVG(time_on_page) as avg_time,
            SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounces,
            SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as conversions,
            SUM(CASE WHEN exit_page = 1 THEN 1 ELSE 0 END) as exits
        ');
        
        if ($start_date) {
            $this->db->where('DATE(date_create) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(date_create) <=', $end_date);
        }
        
        $this->db->where('status', 1);
        $this->db->group_by('page_name');
        $this->db->order_by('visits', 'DESC');
        $this->db->limit($limit);
        
        $pages = $this->db->get($this->table)->result_array();
        
        // Calculate metrics for each page
        foreach ($pages as &$page) {
            $page['bounce_rate'] = $page['unique_visits'] > 0 
                ? round(($page['bounces'] / $page['unique_visits']) * 100, 2) 
                : 0;
            $page['exit_rate'] = $page['visits'] > 0 
                ? round(($page['exits'] / $page['visits']) * 100, 2) 
                : 0;
            $page['avg_time'] = round($page['avg_time'], 2);
        }
        
        return $pages;
    }

    /**
     * Get traffic sources
     * 
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_traffic_sources($start_date = null, $end_date = null)
    {
        $this->db->select('
            referer_page,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews,
            SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as conversions
        ');
        
        if ($start_date) {
            $this->db->where('DATE(date_create) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(date_create) <=', $end_date);
        }
        
        $this->db->where('status', 1);
        $this->db->where('referer_page !=', '');
        $this->db->group_by('referer_page');
        $this->db->order_by('sessions', 'DESC');
        $this->db->limit(20);
        
        $sources = $this->db->get($this->table)->result_array();
        
        // Categorize sources
        foreach ($sources as &$source) {
            $source['source_type'] = $this->categorize_source($source['referer_page']);
            $source['conversion_rate'] = $source['sessions'] > 0 
                ? round(($source['conversions'] / $source['sessions']) * 100, 2) 
                : 0;
        }
        
        return $sources;
    }

    /**
     * Get device statistics
     * 
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_device_stats($start_date = null, $end_date = null)
    {
        $this->db->select('
            device_type,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews,
            AVG(time_on_page) as avg_time,
            SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as conversions
        ');
        
        if ($start_date) {
            $this->db->where('DATE(date_create) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(date_create) <=', $end_date);
        }
        
        $this->db->where('status', 1);
        $this->db->group_by('device_type');
        $this->db->order_by('sessions', 'DESC');
        
        $devices = $this->db->get($this->table)->result_array();
        
        // Calculate total for percentages
        $total_sessions = array_sum(array_column($devices, 'sessions'));
        
        foreach ($devices as &$device) {
            $device['percentage'] = $total_sessions > 0 
                ? round(($device['sessions'] / $total_sessions) * 100, 2) 
                : 0;
            $device['conversion_rate'] = $device['sessions'] > 0 
                ? round(($device['conversions'] / $device['sessions']) * 100, 2) 
                : 0;
        }
        
        return $devices;
    }

    /**
     * Get browser statistics
     * 
     * @param int $limit
     * @return array
     */
    public function get_browser_stats($limit = 10)
    {
        $this->db->select('
            browser,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews
        ');
        $this->db->where('status', 1);
        $this->db->where('browser IS NOT NULL');
        $this->db->group_by('browser');
        $this->db->order_by('sessions', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get geographic distribution
     * 
     * @param int $limit
     * @return array
     */
    public function get_geographic_stats($limit = 10)
    {
        $this->db->select('
            country_code,
            city,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews,
            SUM(CASE WHEN conversion = 1 THEN 1 ELSE 0 END) as conversions
        ');
        $this->db->where('status', 1);
        $this->db->where('country_code IS NOT NULL');
        $this->db->group_by('country_code, city');
        $this->db->order_by('sessions', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get real-time visitors (last 30 minutes)
     * 
     * @return array
     */
    public function get_realtime_visitors()
    {
        $time_threshold = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        
        $this->db->select('
            COUNT(DISTINCT session_id) as active_sessions,
            COUNT(*) as active_pageviews,
            page_name,
            COUNT(CASE WHEN device_type = "mobile" THEN 1 END) as mobile,
            COUNT(CASE WHEN device_type = "desktop" THEN 1 END) as desktop
        ');
        $this->db->where('date_create >=', $time_threshold);
        $this->db->where('status', 1);
        $this->db->group_by('page_name');
        $this->db->order_by('active_pageviews', 'DESC');
        
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get conversion funnel data
     * 
     * @param array $funnel_pages Array of page names in order
     * @return array
     */
    public function get_conversion_funnel($funnel_pages)
    {
        $funnel_data = array();
        
        foreach ($funnel_pages as $index => $page) {
            $this->db->select('COUNT(DISTINCT session_id) as sessions');
            $this->db->where('page_name', $page);
            $this->db->where('status', 1);
            $query = $this->db->get($this->table);
            
            $result = $query->row();
            $funnel_data[] = array(
                'step' => $index + 1,
                'page' => $page,
                'sessions' => $result ? $result->sessions : 0
            );
        }
        
        // Calculate drop-off rates
        for ($i = 1; $i < count($funnel_data); $i++) {
            $previous = $funnel_data[$i - 1]['sessions'];
            $current = $funnel_data[$i]['sessions'];
            
            $funnel_data[$i]['drop_off'] = $previous > 0 
                ? round((($previous - $current) / $previous) * 100, 2) 
                : 0;
        }
        
        return $funnel_data;
    }

    /**
     * Get hourly distribution
     * 
     * @param string $date
     * @return array
     */
    public function get_hourly_distribution($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $this->db->select('
            HOUR(date_create) as hour,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews
        ');
        $this->db->where('DATE(date_create)', $date);
        $this->db->where('status', 1);
        $this->db->group_by('HOUR(date_create)');
        $this->db->order_by('hour', 'ASC');
        
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Search tracking data with filters
     * 
     * @param array $filters
     * @return array
     */
    public function search_with_filters($filters = array())
    {
        $this->db->select('*');
        
        if (isset($filters['start_date'])) {
            $this->db->where('DATE(date_create) >=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $this->db->where('DATE(date_create) <=', $filters['end_date']);
        }
        
        if (isset($filters['device_type'])) {
            $this->db->where('device_type', $filters['device_type']);
        }
        
        if (isset($filters['page_name'])) {
            $this->db->like('page_name', $filters['page_name']);
        }
        
        if (isset($filters['country_code'])) {
            $this->db->where('country_code', $filters['country_code']);
        }
        
        if (isset($filters['conversion'])) {
            $this->db->where('conversion', $filters['conversion']);
        }
        
        $this->db->where('status', 1);
        $this->db->order_by('date_create', 'DESC');
        
        if (isset($filters['limit'])) {
            $this->db->limit($filters['limit']);
        }
        
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Export data to CSV format
     * 
     * @param array $filters
     * @return string
     */
    public function export_to_csv($filters = array())
    {
        $data = $this->search_with_filters($filters);
        
        if (empty($data)) {
            return '';
        }
        
        $csv = '';
        
        // Headers
        $headers = array_keys($data[0]);
        $csv .= implode(',', $headers) . "\n";
        
        // Data rows
        foreach ($data as $row) {
            $csv .= implode(',', array_map(function($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row)) . "\n";
        }
        
        return $csv;
    }

    /**
     * Categorize traffic source
     * 
     * @param string $referer
     * @return string
     */
    private function categorize_source($referer)
    {
        if (empty($referer)) {
            return 'Direct';
        }
        
        $referer = strtolower($referer);
        
        // Social media
        $social_platforms = array('facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest');
        foreach ($social_platforms as $platform) {
            if (strpos($referer, $platform) !== false) {
                return 'Social';
            }
        }
        
        // Search engines
        $search_engines = array('google', 'bing', 'yahoo', 'duckduckgo', 'baidu');
        foreach ($search_engines as $engine) {
            if (strpos($referer, $engine) !== false) {
                return 'Search';
            }
        }
        
        return 'Referral';
    }

    /**
     * Calculate and store daily statistics (for cron job)
     * 
     * @param string $date
     * @return bool
     */
    public function calculate_daily_stats($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d', strtotime('yesterday'));
        }
        
        // Use the stored procedure
        $this->db->query("CALL calculate_daily_stats(?)", array($date));
        
        return true;
    }
}
