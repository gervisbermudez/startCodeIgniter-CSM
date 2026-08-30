<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Admin HTML controller for the Analytics dashboard.
 * Persistence lives in api/v1/AnalyticsController — do not CRUD here.
 * Named AnalyticsAdminController so it does not collide with the API class AnalyticsController.
 */
class AnalyticsAdminController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/admin\/analytics/',
            "required_permissions" => ["SELECT_ANALYTICS"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
    }

    /**
     * GET /admin/analytics
     * Optional query: page_id
     */
    public function index()
    {
        $page_id = $this->input->get('page_id');
        $page_id = $page_id !== null && $page_id !== '' ? (int) $page_id : '';

        $this->renderAdminView(
            'admin.analytics.dashboard',
            lang('analytics_dashboard'),
            lang('menu_analytics'),
            array(
                'page_id' => $page_id,
            )
        );
    }

    /**
     * GET /admin/configuration/analytics → 302 /admin/analytics
     * Keeps old bookmarks working.
     */
    public function legacy_redirect()
    {
        $qs = $this->input->server('QUERY_STRING');
        $target = 'admin/analytics';
        if ($qs) {
            $target .= '?' . $qs;
        }
        redirect($target, 'location', 302);
    }
}
