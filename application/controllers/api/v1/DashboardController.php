<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class DashboardController extends REST_Controller
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
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    /**
     * @api {get} /api/v1/dashboard/:dashboard_id Request Categorie information
     * @apiName GetCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} dashboard_id Categorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function index_get()
    {
        // Intentar obtener datos del caché (v2 = analytics snapshot, not UserTrackingModel::all)
        $cache_key = 'dashboard_data_v2_' . userdata('user_id');
        $cached_data = $this->cache->get($cache_key);

        if ($cached_data !== FALSE) {
            $this->response($cached_data, REST_Controller::HTTP_OK);
            return;
        }
        
        $this->load->model('Admin/CategorieModel');
        $this->load->model('Admin/UserModel');
        $this->load->model('Admin/PageModel');
        $this->load->model('Admin/CustomModelModel');
        $this->load->model('Admin/CustomModelContentModel');
        $this->load->model('Admin/FileModel');
        $this->load->model('Admin/AlbumModel');
        $this->load->model('Admin/EventModel');

        $result = array();

        $Form_conten = new CustomModelContentModel();
        $result['content'] = $Form_conten->all();

        $form = new CustomModelModel();
        $result['forms_types'] = $form->all();

        $dashboard = new CategorieModel();
        $result['dashboards'] = $dashboard->where(array('parent_id' => '0'));

        $user = new UserModel();
        $result['users'] = $user->get_full_info();
        $result['timeline'] = $user->get_timeline(userdata('user_id'));

        $page = new PageModel();
        $result['pages'] = $page->where(["status !=" => "0"]);

        $file = new FileModel();
        $result['files'] = $file->all();

        $album = new AlbumModel();
        $result['albumes'] = $album->all();

        $event = new EventModel();
        $result['events'] = $event->all();

        $empty_chart = array(
            'labels' => array(),
            'datasets' => array(array('tension' => 0.5, 'data' => array())),
        );
        $result['chart1'] = $empty_chart;
        $result['chart2'] = $empty_chart;
        $result['chart3'] = $empty_chart;
        $result['chart4'] = $empty_chart;
        $result['stats'] = array(
            'totalVisitors' => 0,
            'visitorGrowth' => 0,
            'totalRequests' => 0,
            'requestGrowth' => 0,
        );
        $result['kpis'] = array(
            'uniqueVisitors' => 0,
            'totalVisits' => 0,
            'pagesPerSession' => 0,
            'bounceRate' => 0,
            'todayVisits' => 0,
            'yesterdayVisits' => 0,
            'dailyGrowth' => 0,
            'sessions' => 0,
        );
        $result['referrers'] = $empty_chart;
        $result['topPages'] = array();
        $result['has_analytics_data'] = false;
        $result['can_view_analytics'] = function_exists('has_permisions') && has_permisions('SELECT_ANALYTICS');

        if ($result['can_view_analytics']) {
            $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics_model');
            $snapshot = $this->analytics_model->get_home_snapshot(30);
            foreach ($snapshot as $key => $value) {
                $result[$key] = $value;
            }
            $result['has_analytics_data'] = !empty($snapshot['has_data']);
        }

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        // Guardar en caché por 5 minutos (300 segundos)
        $this->cache->save($cache_key, $response, 300);

        $this->response($response, REST_Controller::HTTP_OK);
    }


    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_delete($id = null)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * @api {get} /api/v1/dashboard/type/:type/ Request Categorie information
     * @apiName GetCategorieType
     * @apiGroup Categorie
     *
     * @apiParam {String} type Categorie Categorie type name.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "dashboard_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "dashboard_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:28",
     *               "status": "1"
     *           },
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function filter_get()
    {

        $dashboard = new CategorieModel();
        
        // Sanitizar parámetros GET
        $filters = array();
        $allowed_fields = array('parent_id', 'type', 'status', 'categorie_id');
        
        foreach ($_GET as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $filters[$key] = $this->db->escape_str($value);
            }
        }
        
        $result = $dashboard->where($filters);

        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $result,
            );
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'error_message' => lang('not_found_error'),
                'data' => [],
                'filters' => $filters
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * @api {get} /api/v1/dashboard/:dashboard_id Request Categorie information
     * @apiName GetCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} dashboard_id Categorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function notifications_get()
    {
        $this->load->model('Admin/NotificationsModel');
        $model = new NotificationsModel();
        $rows = $model->inbox(userdata('user_id'), 1, 20);
        $this->response_ok($rows ? $rows : array());
    }

    /**
     *
     * @return Response
     */
    public function notifications_post($id = null)
    {
        $this->load->model('Admin/NotificationsModel');
        $model = new NotificationsModel();
        if ($model->mark_read($id, userdata('user_id'))) {
            $this->response_ok($model);
            return;
        }
        $this->response_error(lang('not_found_error'));
    }

}
