<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Enhanced User Tracking Model with Analytics
 *
 * Bounce = sessions with exactly 1 pageview (not is_bounce on every first hit).
 * Conversion = distinct sessions converted, not row counts.
 *
 * @version 2.1
 */
class UserTrackingModelEnhanced extends MY_Model
{
    public $table = 'user_tracking';
    public $primaryKey = 'user_tracking_id';

    private $page_id_column = null;
    private $page_path_cache = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->page_id_column = $this->db->field_exists('page_id', $this->table);
    }

    /**
     * @param string $start_date
     * @param string $end_date
     * @param int|null $page_id
     * @return array
     */
    public function get_overview_stats($start_date = null, $end_date = null, $page_id = null)
    {
        $range = $this->normalize_date_range($start_date, $end_date);
        $start_date = $range['start'];
        $end_date = $range['end'];

        $this->db->select('
            COUNT(DISTINCT session_id) as total_sessions,
            COUNT(*) as total_pageviews,
            COUNT(DISTINCT client_ip) as unique_visitors,
            AVG(time_on_page) as avg_time_on_page,
            SUM(CASE WHEN device_type = "mobile" THEN 1 ELSE 0 END) as mobile_visits,
            SUM(CASE WHEN device_type = "desktop" THEN 1 ELSE 0 END) as desktop_visits,
            SUM(CASE WHEN device_type = "tablet" THEN 1 ELSE 0 END) as tablet_visits
        ', false);
        $this->apply_date_filter($start_date, $end_date);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $query = $this->db->get($this->table);

        $stats = $query->row_array();
        if (!$stats) {
            $stats = array();
        }

        $total_sessions = isset($stats['total_sessions']) ? (int) $stats['total_sessions'] : 0;
        $total_pageviews = isset($stats['total_pageviews']) ? (int) $stats['total_pageviews'] : 0;

        $bounced = $this->count_bounced_sessions($start_date, $end_date, $page_id);
        $converted = $this->count_converted_sessions($start_date, $end_date, $page_id);

        $stats['total_sessions'] = $total_sessions;
        $stats['total_pageviews'] = $total_pageviews;
        $stats['total_bounces'] = $bounced;
        $stats['total_conversions'] = $converted;
        $stats['bounce_rate'] = $total_sessions > 0
            ? round(($bounced / $total_sessions) * 100, 2)
            : 0;
        $stats['conversion_rate'] = $total_sessions > 0
            ? round(($converted / $total_sessions) * 100, 2)
            : 0;
        $stats['pages_per_session'] = $total_sessions > 0
            ? round($total_pageviews / $total_sessions, 2)
            : 0;
        $stats['avg_time_on_page'] = isset($stats['avg_time_on_page']) ? (float) $stats['avg_time_on_page'] : 0;

        return $stats;
    }

    /**
     * Daily trend. First argument may be an int (days) for backward compatibility.
     *
     * @param mixed $start_date
     * @param string|null $end_date
     * @param int|null $page_id
     * @return array
     */
    public function get_daily_trend($start_date = null, $end_date = null, $page_id = null)
    {
        if (is_numeric($start_date) && $end_date === null) {
            $days = (int) $start_date;
            $start_date = date('Y-m-d', strtotime('-' . $days . ' days'));
            $end_date = date('Y-m-d');
        }

        $range = $this->normalize_date_range($start_date, $end_date);

        $this->db->select('
            DATE(date_create) as date,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews,
            COUNT(DISTINCT client_ip) as unique_visitors,
            AVG(time_on_page) as avg_time
        ', false);
        $this->apply_date_filter($range['start'], $range['end']);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->group_by('DATE(date_create)');
        $this->db->order_by('date', 'ASC');

        return $this->db->get($this->table)->result_array();
    }

    public function get_popular_pages($limit = 10, $start_date = null, $end_date = null, $page_id = null)
    {
        $this->db->select('
            page_name,
            COUNT(*) as visits,
            COUNT(DISTINCT session_id) as unique_visits,
            AVG(time_on_page) as avg_time,
            SUM(CASE WHEN exit_page = 1 THEN 1 ELSE 0 END) as exits
        ', false);

        $this->apply_date_filter($start_date, $end_date);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->group_by('page_name');
        $this->db->order_by('visits', 'DESC');
        $this->db->limit($limit);

        $pages = $this->db->get($this->table)->result_array();

        foreach ($pages as &$page) {
            $page['bounce_rate'] = $this->page_bounce_rate($page['page_name'], $start_date, $end_date);
            $page['exit_rate'] = $page['visits'] > 0
                ? round(($page['exits'] / $page['visits']) * 100, 2)
                : 0;
            $page['avg_time'] = round($page['avg_time'], 2);
            $page['conversions'] = 0;
        }

        return $pages;
    }

    public function get_traffic_sources($start_date = null, $end_date = null, $page_id = null)
    {
        $this->db->select('
            referer_page,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews
        ', false);

        $this->apply_date_filter($start_date, $end_date);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->where('referer_page !=', '');
        $this->db->group_by('referer_page');
        $this->db->order_by('sessions', 'DESC');
        $this->db->limit(20);

        $sources = $this->db->get($this->table)->result_array();

        foreach ($sources as &$source) {
            $source['source_type'] = $this->categorize_source($source['referer_page']);
            $source['conversions'] = 0;
            $source['conversion_rate'] = 0;
        }

        return $sources;
    }

    public function get_device_stats($start_date = null, $end_date = null, $page_id = null)
    {
        $this->db->select('
            device_type,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews,
            AVG(time_on_page) as avg_time
        ', false);

        $this->apply_date_filter($start_date, $end_date);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->group_by('device_type');
        $this->db->order_by('sessions', 'DESC');

        $devices = $this->db->get($this->table)->result_array();

        $total_sessions = 0;
        foreach ($devices as $device) {
            $total_sessions += (int) $device['sessions'];
        }

        foreach ($devices as &$device) {
            $device['percentage'] = $total_sessions > 0
                ? round(($device['sessions'] / $total_sessions) * 100, 2)
                : 0;
            $device['conversion_rate'] = 0;
        }

        return $devices;
    }

    public function get_browser_stats($limit = 10, $start_date = null, $end_date = null, $page_id = null)
    {
        $this->db->select('
            browser,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews
        ', false);
        $this->apply_date_filter($start_date, $end_date);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->where('browser IS NOT NULL');
        $this->db->group_by('browser');
        $this->db->order_by('sessions', 'DESC');
        $this->db->limit($limit);

        return $this->db->get($this->table)->result_array();
    }

    public function get_geographic_stats($limit = 10)
    {
        $this->db->select('
            country_code,
            city,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews
        ', false);
        $this->db->where('status', 1);
        $this->db->where('country_code IS NOT NULL');
        $this->db->group_by('country_code, city');
        $this->db->order_by('sessions', 'DESC');
        $this->db->limit($limit);

        return $this->db->get($this->table)->result_array();
    }

    public function get_realtime_visitors($page_id = null)
    {
        $time_threshold = date('Y-m-d H:i:s', strtotime('-30 minutes'));

        $this->db->select('
            COUNT(DISTINCT session_id) as active_sessions,
            COUNT(*) as active_pageviews,
            page_name,
            COUNT(CASE WHEN device_type = "mobile" THEN 1 END) as mobile,
            COUNT(CASE WHEN device_type = "desktop" THEN 1 END) as desktop
        ', false);
        $this->db->where('date_create >=', $time_threshold);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->group_by('page_name');
        $this->db->order_by('active_pageviews', 'DESC');

        return $this->db->get($this->table)->result_array();
    }

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

        for ($i = 1; $i < count($funnel_data); $i++) {
            $previous = $funnel_data[$i - 1]['sessions'];
            $current = $funnel_data[$i]['sessions'];

            $funnel_data[$i]['drop_off'] = $previous > 0
                ? round((($previous - $current) / $previous) * 100, 2)
                : 0;
        }

        return $funnel_data;
    }

    public function get_hourly_distribution($date = null, $page_id = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $this->db->select('
            HOUR(date_create) as hour,
            COUNT(DISTINCT session_id) as sessions,
            COUNT(*) as pageviews
        ', false);
        $this->db->where('DATE(date_create)', $date);
        $this->apply_page_filter($page_id);
        $this->db->where('status', 1);
        $this->db->group_by('HOUR(date_create)');
        $this->db->order_by('hour', 'ASC');

        return $this->db->get($this->table)->result_array();
    }

    /**
     * Aggregated JS events by category/action.
     */
    public function get_event_stats($start_date = null, $end_date = null, $limit = 20)
    {
        $range = $this->normalize_date_range($start_date, $end_date);

        $this->db->select('
            event_category,
            event_action,
            COUNT(*) as total,
            COUNT(DISTINCT session_id) as sessions
        ', false);
        $this->db->from('user_tracking_events');
        $this->db->where('DATE(created_at) >=', $range['start']);
        $this->db->where('DATE(created_at) <=', $range['end']);
        $this->db->group_by('event_category, event_action');
        $this->db->order_by('total', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    /**
     * Compact payload for the admin home dashboard (same metrics as /api/v1/analytics).
     * Does not load every tracking row into PHP.
     *
     * @param int $days
     * @return array
     */
    public function get_home_snapshot($days = 30)
    {
        $days = (int) $days;
        if ($days < 1) {
            $days = 30;
        }

        $end = date('Y-m-d');
        $start = date('Y-m-d', strtotime('-' . $days . ' days'));
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $week_start = date('Y-m-d', strtotime('-7 days'));
        $prev_start = date('Y-m-d', strtotime('-14 days'));
        $prev_end = date('Y-m-d', strtotime('-8 days'));

        $overview = $this->get_overview_stats($start, $end);
        $today_stats = $this->get_overview_stats($today, $today);
        $yesterday_stats = $this->get_overview_stats($yesterday, $yesterday);
        $this_week = $this->get_overview_stats($week_start, $end);
        $prev_week = $this->get_overview_stats($prev_start, $prev_end);
        $trend = $this->get_daily_trend($start, $end);
        $devices = $this->get_device_stats($start, $end);
        $popular = $this->get_popular_pages(7, $start, $end);
        $sources = $this->get_traffic_sources($start, $end);

        $today_views = isset($today_stats['total_pageviews']) ? (int) $today_stats['total_pageviews'] : 0;
        $yesterday_views = isset($yesterday_stats['total_pageviews']) ? (int) $yesterday_stats['total_pageviews'] : 0;
        $daily_growth = 0;
        if ($yesterday_views > 0) {
            $daily_growth = round((($today_views - $yesterday_views) / $yesterday_views) * 100, 1);
        } elseif ($today_views > 0) {
            $daily_growth = 100;
        }

        $this_sessions = isset($this_week['total_sessions']) ? (int) $this_week['total_sessions'] : 0;
        $prev_sessions = isset($prev_week['total_sessions']) ? (int) $prev_week['total_sessions'] : 0;
        $visitor_growth = 0;
        if ($prev_sessions > 0) {
            $visitor_growth = round((($this_sessions - $prev_sessions) / $prev_sessions) * 100);
        } elseif ($this_sessions > 0) {
            $visitor_growth = 100;
        }

        $trend_labels = array();
        $trend_sessions = array();
        $trend_pageviews = array();
        if (is_array($trend)) {
            foreach ($trend as $row) {
                $trend_labels[] = isset($row['date']) ? $row['date'] : '';
                $trend_sessions[] = isset($row['sessions']) ? (int) $row['sessions'] : 0;
                $trend_pageviews[] = isset($row['pageviews']) ? (int) $row['pageviews'] : 0;
            }
        }

        $device_labels = array();
        $device_data = array();
        if (is_array($devices)) {
            foreach ($devices as $device) {
                $label = !empty($device['device_type']) ? $device['device_type'] : 'unknown';
                $device_labels[] = $label;
                $device_data[] = isset($device['sessions']) ? (int) $device['sessions'] : 0;
            }
        }

        $page_labels = array();
        $page_data = array();
        $top_pages = array();
        if (is_array($popular)) {
            foreach ($popular as $page) {
                $name = isset($page['page_name']) ? $page['page_name'] : '';
                if ($name === '') {
                    continue;
                }
                $lower = strtolower($name);
                if (
                    strpos($lower, 'favicon') !== false
                    || strpos($lower, 'robots.txt') !== false
                    || strpos($lower, 'well-known') !== false
                    || strpos($lower, 'sitemap.xml') !== false
                ) {
                    continue;
                }
                $visits = isset($page['visits']) ? (int) $page['visits'] : 0;
                $page_labels[] = $name;
                $page_data[] = $visits;
                $top_pages[$name] = $visits;
            }
        }

        $ref_labels = array();
        $ref_data = array();
        if (is_array($sources)) {
            $sources = array_slice($sources, 0, 5);
            foreach ($sources as $source) {
                $raw = isset($source['referer_page']) ? $source['referer_page'] : '';
                $host = $raw ? parse_url($raw, PHP_URL_HOST) : '';
                $ref_labels[] = $host ? $host : ($raw ? $raw : 'Direct');
                $ref_data[] = isset($source['sessions']) ? (int) $source['sessions'] : 0;
            }
        }

        $unique = isset($overview['unique_visitors']) ? (int) $overview['unique_visitors'] : 0;
        $pageviews = isset($overview['total_pageviews']) ? (int) $overview['total_pageviews'] : 0;
        $sessions = isset($overview['total_sessions']) ? (int) $overview['total_sessions'] : 0;

        return array(
            'kpis' => array(
                'uniqueVisitors' => $unique,
                'totalVisits' => $pageviews,
                'pagesPerSession' => isset($overview['pages_per_session']) ? $overview['pages_per_session'] : 0,
                'bounceRate' => isset($overview['bounce_rate']) ? $overview['bounce_rate'] : 0,
                'todayVisits' => $today_views,
                'yesterdayVisits' => $yesterday_views,
                'dailyGrowth' => $daily_growth,
                'sessions' => $sessions,
            ),
            'stats' => array(
                'totalVisitors' => $unique,
                'visitorGrowth' => $visitor_growth,
                'totalRequests' => $pageviews,
                'requestGrowth' => $visitor_growth,
            ),
            'chart1' => $this->chart_dataset($trend_labels, $trend_pageviews),
            'chart2' => $this->chart_dataset($trend_labels, $trend_sessions),
            'chart3' => $this->chart_dataset($device_labels, $device_data),
            'chart4' => $this->chart_dataset($page_labels, $page_data),
            'topPages' => $top_pages,
            'referrers' => $this->chart_dataset($ref_labels, $ref_data),
            'has_data' => $pageviews > 0,
        );
    }

    /**
     * @param array $labels
     * @param array $data
     * @return array
     */
    private function chart_dataset($labels, $data)
    {
        return array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'tension' => 0.5,
                    'data' => $data,
                ),
            ),
        );
    }

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

        if (isset($filters['page_id'])) {
            $this->apply_page_filter($filters['page_id']);
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

    public function export_to_csv($filters = array())
    {
        $data = $this->search_with_filters($filters);

        if (empty($data)) {
            return '';
        }

        $csv = '';
        $headers = array_keys($data[0]);
        $csv .= implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $csv .= implode(',', array_map(function ($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row)) . "\n";
        }

        return $csv;
    }

    public function calculate_daily_stats($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d', strtotime('yesterday'));
        }

        $this->db->query("CALL calculate_daily_stats(?)", array($date));

        return true;
    }

    private function normalize_date_range($start_date, $end_date)
    {
        if (!$start_date) {
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }
        return array('start' => $start_date, 'end' => $end_date);
    }

    private function apply_date_filter($start_date, $end_date)
    {
        if ($start_date) {
            $this->db->where('DATE(date_create) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(date_create) <=', $end_date);
        }
    }

    /**
     * Filter by page_id column when present, else by page.path vs requested_url / page_name.
     */
    private function apply_page_filter($page_id)
    {
        if ($page_id === null || $page_id === '') {
            return;
        }

        $page_id = (int) $page_id;
        if ($page_id < 1) {
            return;
        }

        $paths = $this->page_path_variants($page_id);

        if ($this->tracking_has_page_id()) {
            $this->db->group_start();
            $this->db->where('page_id', $page_id);
            foreach ($paths as $path) {
                $this->db->or_where('page_name', $path);
                $this->db->or_where('requested_url', $path);
            }
            $this->db->group_end();
            return;
        }

        if (empty($paths)) {
            $this->db->where('1 =', 0);
            return;
        }

        $this->db->group_start();
        foreach ($paths as $i => $path) {
            if ($i === 0) {
                $this->db->where('page_name', $path);
            } else {
                $this->db->or_where('page_name', $path);
            }
            $this->db->or_where('requested_url', $path);
        }
        $this->db->group_end();
    }

    private function page_path_variants($page_id)
    {
        $page_id = (int) $page_id;
        if (isset($this->page_path_cache[$page_id])) {
            return $this->page_path_cache[$page_id];
        }

        $query = $this->db->query('SELECT path FROM page WHERE page_id = ? LIMIT 1', array($page_id));
        if (!$query || $query->num_rows() === 0) {
            $this->page_path_cache[$page_id] = array();
            return array();
        }

        $path = $query->row()->path;
        $trimmed = trim($path, '/');
        $variants = array($path);
        if ($trimmed !== '') {
            $variants[] = $trimmed;
            $variants[] = '/' . $trimmed;
            $variants[] = $trimmed . '/index';
            $variants[] = '/' . $trimmed . '/index';
        } else {
            $variants[] = '/';
        }

        $this->page_path_cache[$page_id] = array_unique($variants);
        return $this->page_path_cache[$page_id];
    }

    private function tracking_has_page_id()
    {
        if ($this->page_id_column === null) {
            $this->page_id_column = $this->db->field_exists('page_id', $this->table);
        }
        return (bool) $this->page_id_column;
    }

    /**
     * Sessions in range with exactly 1 pageview (optionally of this page).
     */
    private function count_bounced_sessions($start_date, $end_date, $page_id = null)
    {
        if ($page_id) {
            $sql = "SELECT COUNT(DISTINCT us.session_id) AS bounced
                    FROM user_sessions us
                    INNER JOIN {$this->table} ut ON ut.session_id = us.session_id AND ut.status = 1
                    WHERE us.total_pages <= 1
                      AND DATE(ut.date_create) >= ?
                      AND DATE(ut.date_create) <= ?
                      {$this->page_filter_sql($page_id, 3, 'ut')}";
            $bindings = array($start_date, $end_date);
            $this->append_page_bindings($bindings, $page_id);
            $query = $this->db->query($sql, $bindings);
            $row = $query->row_array();
            return $row ? (int) $row['bounced'] : 0;
        }

        $sql = "SELECT COUNT(*) AS bounced FROM (
                    SELECT session_id
                    FROM {$this->table}
                    WHERE status = 1
                      AND DATE(date_create) >= ?
                      AND DATE(date_create) <= ?
                    GROUP BY session_id
                    HAVING COUNT(*) = 1
                ) bounced_sessions";

        $query = $this->db->query($sql, array($start_date, $end_date));
        $row = $query->row_array();
        return $row ? (int) $row['bounced'] : 0;
    }

    /**
     * Distinct sessions with conversion=1 on a hit OR user_sessions.converted=1.
     */
    private function count_converted_sessions($start_date, $end_date, $page_id = null)
    {
        $sql = "SELECT COUNT(DISTINCT session_id) AS converted FROM (
                    SELECT ut.session_id
                    FROM {$this->table} ut
                    WHERE ut.status = 1
                      AND ut.conversion = 1
                      AND DATE(ut.date_create) >= ?
                      AND DATE(ut.date_create) <= ?
                      {$this->page_filter_sql($page_id, 3, 'ut')}
                    UNION
                    SELECT us.session_id
                    FROM user_sessions us
                    INNER JOIN {$this->table} ut2 ON ut2.session_id = us.session_id AND ut2.status = 1
                    WHERE us.converted = 1
                      AND DATE(ut2.date_create) >= ?
                      AND DATE(ut2.date_create) <= ?
                      {$this->page_filter_sql($page_id, 3, 'ut2')}
                ) converted_sessions";

        $bindings = array($start_date, $end_date);
        $this->append_page_bindings($bindings, $page_id);
        $bindings[] = $start_date;
        $bindings[] = $end_date;
        $this->append_page_bindings($bindings, $page_id);

        $query = $this->db->query($sql, $bindings);
        $row = $query->row_array();
        return $row ? (int) $row['converted'] : 0;
    }

    private function page_bounce_rate($page_name, $start_date, $end_date)
    {
        $sql = "SELECT
                    COUNT(*) AS sessions,
                    SUM(CASE WHEN us.total_pages <= 1 THEN 1 ELSE 0 END) AS bounced
                FROM (
                    SELECT DISTINCT session_id
                    FROM {$this->table}
                    WHERE status = 1
                      AND page_name = ?
                      AND DATE(date_create) >= ?
                      AND DATE(date_create) <= ?
                ) t
                INNER JOIN user_sessions us ON us.session_id = t.session_id";

        $range = $this->normalize_date_range($start_date, $end_date);
        $query = $this->db->query($sql, array($page_name, $range['start'], $range['end']));
        $row = $query->row_array();
        if (!$row || (int) $row['sessions'] === 0) {
            return 0;
        }
        return round(((int) $row['bounced'] / (int) $row['sessions']) * 100, 2);
    }

    private function page_filter_sql($page_id, $placeholder_offset, $alias = '')
    {
        if ($page_id === null || $page_id === '' || (int) $page_id < 1) {
            return '';
        }

        $prefix = $alias ? $alias . '.' : '';
        $paths = $this->page_path_variants((int) $page_id);
        $parts = array();

        if ($this->tracking_has_page_id()) {
            $parts[] = $prefix . 'page_id = ?';
        }

        foreach ($paths as $path) {
            $parts[] = $prefix . 'page_name = ?';
            $parts[] = $prefix . 'requested_url = ?';
        }

        if (empty($parts)) {
            return '';
        }

        return ' AND (' . implode(' OR ', $parts) . ')';
    }

    private function append_page_bindings(&$bindings, $page_id)
    {
        if ($page_id === null || $page_id === '' || (int) $page_id < 1) {
            return;
        }

        $paths = $this->page_path_variants((int) $page_id);
        if ($this->tracking_has_page_id()) {
            $bindings[] = (int) $page_id;
        }
        foreach ($paths as $path) {
            $bindings[] = $path;
            $bindings[] = $path;
        }
    }

    private function categorize_source($referer)
    {
        if (empty($referer)) {
            return 'Direct';
        }

        $referer = strtolower($referer);

        $social_platforms = array('facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest');
        foreach ($social_platforms as $platform) {
            if (strpos($referer, $platform) !== false) {
                return 'Social';
            }
        }

        $search_engines = array('google', 'bing', 'yahoo', 'duckduckgo', 'baidu');
        foreach ($search_engines as $engine) {
            if (strpos($referer, $engine) !== false) {
                return 'Search';
            }
        }

        return 'Referral';
    }
}
