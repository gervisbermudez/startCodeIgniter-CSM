<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Analytics Helper
 * 
 * Funciones helper para facilitar el uso del sistema de analytics
 */

if (!function_exists('track_event')) {
    /**
     * Track custom event
     * 
     * @param string $category
     * @param string $action
     * @param string $label
     * @param int $value
     * @param array $metadata
     * @return bool
     */
    function track_event($category, $action, $label = null, $value = null, $metadata = null)
    {
        $CI =& get_instance();
        
        if (!isset($CI->tracker)) {
            $CI->load->library('Track_Visitor_Enhanced', null, 'tracker');
        }
        
        return $CI->tracker->track_event($category, $action, $label, $value, $metadata);
    }
}

if (!function_exists('track_conversion')) {
    /**
     * Track conversion
     * 
     * @return bool
     */
    function track_conversion()
    {
        $CI =& get_instance();
        
        if (!isset($CI->tracker)) {
            $CI->load->library('Track_Visitor_Enhanced', null, 'tracker');
        }
        
        return $CI->tracker->track_conversion();
    }
}

if (!function_exists('get_session_id')) {
    /**
     * Get current tracking session ID
     * 
     * @return string|null
     */
    function get_session_id()
    {
        $CI =& get_instance();
        
        if (!isset($CI->tracker)) {
            $CI->load->library('Track_Visitor_Enhanced', null, 'tracker');
        }
        
        return $CI->tracker->get_session_id();
    }
}

if (!function_exists('get_analytics_stats')) {
    /**
     * Get analytics statistics
     * 
     * @param string $type Type of stats (overview, trend, pages, devices, etc.)
     * @param array $params Additional parameters
     * @return array
     */
    function get_analytics_stats($type = 'overview', $params = array())
    {
        $CI =& get_instance();
        $CI->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
        
        switch ($type) {
            case 'overview':
                $start = isset($params['start_date']) ? $params['start_date'] : null;
                $end = isset($params['end_date']) ? $params['end_date'] : null;
                return $CI->analytics->get_overview_stats($start, $end);
                
            case 'trend':
                $days = isset($params['days']) ? $params['days'] : 30;
                return $CI->analytics->get_daily_trend($days);
                
            case 'pages':
                $limit = isset($params['limit']) ? $params['limit'] : 10;
                $start = isset($params['start_date']) ? $params['start_date'] : null;
                $end = isset($params['end_date']) ? $params['end_date'] : null;
                return $CI->analytics->get_popular_pages($limit, $start, $end);
                
            case 'devices':
                $start = isset($params['start_date']) ? $params['start_date'] : null;
                $end = isset($params['end_date']) ? $params['end_date'] : null;
                return $CI->analytics->get_device_stats($start, $end);
                
            case 'realtime':
                return $CI->analytics->get_realtime_visitors();
                
            default:
                return array();
        }
    }
}

if (!function_exists('format_analytics_metric')) {
    /**
     * Format analytics metric for display
     * 
     * @param mixed $value
     * @param string $type (number, percentage, time, currency)
     * @return string
     */
    function format_analytics_metric($value, $type = 'number')
    {
        switch ($type) {
            case 'number':
                return number_format($value);
                
            case 'percentage':
                return number_format($value, 2) . '%';
                
            case 'time':
                $seconds = round($value);
                $minutes = floor($seconds / 60);
                $remainingSeconds = $seconds % 60;
                if ($minutes > 0) {
                    return "{$minutes}m {$remainingSeconds}s";
                }
                return "{$seconds}s";
                
            case 'currency':
                return '$' . number_format($value, 2);
                
            default:
                return $value;
        }
    }
}

if (!function_exists('get_visitor_device_type')) {
    /**
     * Get current visitor's device type
     * 
     * @return string (desktop, mobile, tablet, bot)
     */
    function get_visitor_device_type()
    {
        $CI =& get_instance();
        $CI->load->library('user_agent');
        
        if ($CI->agent->is_robot()) {
            return 'bot';
        }
        
        if ($CI->agent->is_mobile()) {
            // Check if tablet
            $user_agent = $CI->agent->agent_string();
            $tablet_keywords = array('ipad', 'tablet', 'kindle', 'playbook', 'nexus 7', 'nexus 10');
            
            foreach ($tablet_keywords as $keyword) {
                if (stripos($user_agent, $keyword) !== false) {
                    return 'tablet';
                }
            }
            
            return 'mobile';
        }
        
        return 'desktop';
    }
}

if (!function_exists('is_new_visitor')) {
    /**
     * Check if current visitor is new or returning
     * 
     * @return bool
     */
    function is_new_visitor()
    {
        $CI =& get_instance();
        $CI->load->helper('cookie');
        
        return !get_cookie('returning_visitor');
    }
}

if (!function_exists('get_analytics_period')) {
    /**
     * Get predefined date periods
     * 
     * @param string $period (today, yesterday, week, month, year, custom)
     * @param string $custom_start Custom start date
     * @param string $custom_end Custom end date
     * @return array ['start' => date, 'end' => date]
     */
    function get_analytics_period($period = 'month', $custom_start = null, $custom_end = null)
    {
        $today = date('Y-m-d');
        
        switch ($period) {
            case 'today':
                return array(
                    'start' => $today,
                    'end' => $today
                );
                
            case 'yesterday':
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                return array(
                    'start' => $yesterday,
                    'end' => $yesterday
                );
                
            case 'week':
                return array(
                    'start' => date('Y-m-d', strtotime('-7 days')),
                    'end' => $today
                );
                
            case 'month':
                return array(
                    'start' => date('Y-m-d', strtotime('-30 days')),
                    'end' => $today
                );
                
            case 'year':
                return array(
                    'start' => date('Y-m-d', strtotime('-365 days')),
                    'end' => $today
                );
                
            case 'custom':
                return array(
                    'start' => $custom_start ?: $today,
                    'end' => $custom_end ?: $today
                );
                
            default:
                return array(
                    'start' => date('Y-m-d', strtotime('-30 days')),
                    'end' => $today
                );
        }
    }
}

if (!function_exists('export_analytics_csv')) {
    /**
     * Export analytics data to CSV
     * 
     * @param array $filters
     * @param string $filename
     */
    function export_analytics_csv($filters = array(), $filename = null)
    {
        $CI =& get_instance();
        $CI->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
        
        $csv = $CI->analytics->export_to_csv($filters);
        
        if (empty($csv)) {
            return false;
        }
        
        if (!$filename) {
            $filename = 'analytics_export_' . date('Y-m-d') . '.csv';
        }
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $csv;
        exit;
    }
}

if (!function_exists('get_bounce_rate_color')) {
    /**
     * Get color class based on bounce rate
     * 
     * @param float $bounce_rate
     * @return string
     */
    function get_bounce_rate_color($bounce_rate)
    {
        if ($bounce_rate < 26) {
            return 'green'; // Excellent
        } elseif ($bounce_rate < 41) {
            return 'light-green'; // Good
        } elseif ($bounce_rate < 56) {
            return 'orange'; // Average
        } elseif ($bounce_rate < 70) {
            return 'deep-orange'; // Below average
        } else {
            return 'red'; // Poor
        }
    }
}

if (!function_exists('get_conversion_rate_color')) {
    /**
     * Get color class based on conversion rate
     * 
     * @param float $conversion_rate
     * @return string
     */
    function get_conversion_rate_color($conversion_rate)
    {
        if ($conversion_rate > 10) {
            return 'green'; // Excellent
        } elseif ($conversion_rate > 5) {
            return 'light-green'; // Good
        } elseif ($conversion_rate > 2) {
            return 'orange'; // Average
        } else {
            return 'red'; // Below average
        }
    }
}

if (!function_exists('analytics_widget')) {
    /**
     * Generate HTML for analytics widget
     * 
     * @param string $type Widget type
     * @param array $data Widget data
     * @return string HTML
     */
    function analytics_widget($type, $data = array())
    {
        $html = '<div class="analytics-widget ' . $type . '">';
        
        switch ($type) {
            case 'metric':
                $html .= '<div class="metric-label">' . $data['label'] . '</div>';
                $html .= '<div class="metric-value">' . $data['value'] . '</div>';
                if (isset($data['change'])) {
                    $change_class = $data['change'] >= 0 ? 'positive' : 'negative';
                    $html .= '<div class="metric-change ' . $change_class . '">';
                    $html .= ($data['change'] >= 0 ? '+' : '') . $data['change'] . '%';
                    $html .= '</div>';
                }
                break;
                
            case 'sparkline':
                // Requires Chart.js or similar
                $html .= '<canvas id="' . $data['id'] . '" width="100" height="30"></canvas>';
                break;
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
