<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Analytics API Controller
 * 
 * Provides comprehensive analytics endpoints for user tracking data
 * 
 * @version 2.0
 */
class AnalyticsController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics');
    }
    
    /**
     * Test endpoint to check if API is working
     */
    public function index_get()
    {
        $this->response_ok([
            'status' => 'ok',
            'message' => 'Analytics API is working',
            'version' => '2.0',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * GET /api/v1/analytics/overview
     * Get overview statistics
     * 
     * Query params: start_date, end_date
     */
    public function overview_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        
        $stats = $this->analytics->get_overview_stats($start_date, $end_date);
        
        $this->response_ok($stats);
    }

    /**
     * GET /api/v1/analytics/trend
     * Get daily trend data
     * 
     * Query params: days (default: 30)
     */
    public function trend_get()
    {
        $days = $this->get('days') ?: 30;
        
        $trend = $this->analytics->get_daily_trend($days);
        
        $this->response_ok($trend);
    }

    /**
     * GET /api/v1/analytics/popular-pages
     * Get most popular pages
     * 
     * Query params: limit, start_date, end_date
     */
    public function popular_pages_get()
    {
        $limit = $this->get('limit') ?: 10;
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        
        $pages = $this->analytics->get_popular_pages($limit, $start_date, $end_date);
        
        $this->response_ok($pages);
    }

    /**
     * GET /api/v1/analytics/traffic-sources
     * Get traffic sources
     * 
     * Query params: start_date, end_date
     */
    public function traffic_sources_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        
        $sources = $this->analytics->get_traffic_sources($start_date, $end_date);
        
        $this->response_ok($sources);
    }

    /**
     * GET /api/v1/analytics/devices
     * Get device statistics
     * 
     * Query params: start_date, end_date
     */
    public function devices_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        
        $devices = $this->analytics->get_device_stats($start_date, $end_date);
        
        $this->response_ok($devices);
    }

    /**
     * GET /api/v1/analytics/browsers
     * Get browser statistics
     * 
     * Query params: limit
     */
    public function browsers_get()
    {
        $limit = $this->get('limit') ?: 10;
        
        $browsers = $this->analytics->get_browser_stats($limit);
        
        $this->response_ok($browsers);
    }

    /**
     * GET /api/v1/analytics/geographic
     * Get geographic distribution
     * 
     * Query params: limit
     */
    public function geographic_get()
    {
        $limit = $this->get('limit') ?: 10;
        
        $locations = $this->analytics->get_geographic_stats($limit);
        
        $this->response_ok($locations);
    }

    /**
     * GET /api/v1/analytics/realtime
     * Get real-time visitors (last 30 minutes)
     */
    public function realtime_get()
    {
        $realtime = $this->analytics->get_realtime_visitors();
        
        $this->response_ok($realtime);
    }

    /**
     * GET /api/v1/analytics/hourly
     * Get hourly distribution for a specific date
     * 
     * Query params: date (YYYY-MM-DD)
     */
    public function hourly_get()
    {
        $date = $this->get('date') ?: date('Y-m-d');
        
        $hourly = $this->analytics->get_hourly_distribution($date);
        
        $this->response_ok($hourly);
    }

    /**
     * POST /api/v1/analytics/funnel
     * Get conversion funnel data
     * 
     * Body: { "pages": ["page1", "page2", "page3"] }
     */
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

    /**
     * GET /api/v1/analytics/export
     * Export data to CSV
     * 
     * Query params: start_date, end_date, device_type, page_name, etc.
     */
    public function export_get()
    {
        $filters = array(
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date'),
            'device_type' => $this->get('device_type'),
            'page_name' => $this->get('page_name'),
            'country_code' => $this->get('country_code'),
            'conversion' => $this->get('conversion'),
            'limit' => $this->get('limit') ?: 1000
        );
        
        // Remove null values
        $filters = array_filter($filters, function($value) {
            return $value !== null;
        });
        
        $csv = $this->analytics->export_to_csv($filters);
        
        if (empty($csv)) {
            $this->response_error('No data to export');
            return;
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="analytics_export_' . date('Y-m-d') . '.csv"');
        echo $csv;
        exit;
    }

    /**
     * GET /api/v1/analytics/search
     * Search tracking data with filters
     * 
     * Query params: various filters
     */
    public function search_get()
    {
        $filters = array(
            'start_date' => $this->get('start_date'),
            'end_date' => $this->get('end_date'),
            'device_type' => $this->get('device_type'),
            'page_name' => $this->get('page_name'),
            'country_code' => $this->get('country_code'),
            'conversion' => $this->get('conversion'),
            'limit' => $this->get('limit') ?: 100
        );
        
        // Remove null values
        $filters = array_filter($filters, function($value) {
            return $value !== null;
        });
        
        $results = $this->analytics->search_with_filters($filters);
        
        $this->response_ok($results);
    }

    /**
     * GET /api/v1/analytics/dashboard
     * Get all data needed for dashboard in one call
     * 
     * Query params: start_date, end_date
     */
    public function dashboard_get()
    {
        $start_date = $this->get('start_date');
        $end_date = $this->get('end_date');
        
        $dashboard = array(
            'overview' => $this->analytics->get_overview_stats($start_date, $end_date),
            'trend' => $this->analytics->get_daily_trend(30),
            'popular_pages' => $this->analytics->get_popular_pages(10, $start_date, $end_date),
            'devices' => $this->analytics->get_device_stats($start_date, $end_date),
            'traffic_sources' => $this->analytics->get_traffic_sources($start_date, $end_date),
            'realtime' => $this->analytics->get_realtime_visitors(),
            'hourly' => $this->analytics->get_hourly_distribution(date('Y-m-d'))
        );
        
        $this->response_ok($dashboard);
    }

    /**
     * POST /api/v1/analytics/event
     * Track custom event
     * 
     * Body: { "category": "Form", "action": "Submit", "label": "Contact", "value": 1 }
     */
    public function event_post()
    {
        $category = $this->post('category');
        $action = $this->post('action');
        $label = $this->post('label');
        $value = $this->post('value');
        $metadata = $this->post('metadata');
        
        if (empty($category) || empty($action)) {
            $this->response_error('Category and action are required');
            return;
        }
        
        // Load tracking library
        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        
        $result = $this->tracker->track_event($category, $action, $label, $value, $metadata);
        
        if ($result) {
            $this->response_ok(['message' => 'Event tracked successfully']);
        } else {
            $this->response_error('Failed to track event');
        }
    }

    /**
     * POST /api/v1/analytics/conversion
     * Track conversion
     */
    public function conversion_post()
    {
        // Load tracking library
        $this->load->library('Track_Visitor_Enhanced', null, 'tracker');
        
        $result = $this->tracker->track_conversion();
        
        if ($result) {
            $this->response_ok(['message' => 'Conversion tracked successfully']);
        } else {
            $this->response_error('Failed to track conversion');
        }
    }
}
