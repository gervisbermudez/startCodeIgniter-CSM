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
            $this->response(array(
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ), REST_Controller::HTTP_UNAUTHORIZED);
            echo $this->output->get_output();
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
        $caps = dashboard_capabilities();
        $perm_key = dashboard_perm_cache_key();
        $cache_key = 'dashboard_data_v6_' . userdata('user_id') . '_' . $perm_key;
        $cached_data = $this->cache->get($cache_key);

        if ($cached_data !== false) {
            $this->response($this->dashboard_with_layout($cached_data), REST_Controller::HTTP_OK);
            return;
        }

        $empty_chart = array(
            'labels' => array(),
            'datasets' => array(array('tension' => 0.5, 'data' => array())),
        );

        $result = array(
            'capabilities' => $caps,
            'counts' => array(
                'users' => 0,
                'pages' => 0,
                'files' => 0,
                'events' => 0,
                'albumes' => 0,
                'content' => 0,
            ),
            'users' => array(),
            'pages' => array(),
            'files' => array(),
            'albumes' => array(),
            'events' => array(),
            'content' => array(),
            'forms_types' => array(),
            'collections' => array(),
            'timeline' => array(),
            'chart1' => $empty_chart,
            'chart2' => $empty_chart,
            'chart3' => $empty_chart,
            'chart4' => $empty_chart,
            'stats' => array(
                'totalVisitors' => 0,
                'visitorGrowth' => 0,
                'totalRequests' => 0,
                'requestGrowth' => 0,
            ),
            'kpis' => array(
                'uniqueVisitors' => 0,
                'totalVisits' => 0,
                'pagesPerSession' => 0,
                'bounceRate' => 0,
                'todayVisits' => 0,
                'yesterdayVisits' => 0,
                'dailyGrowth' => 0,
                'sessions' => 0,
            ),
            'referrers' => $empty_chart,
            'topPages' => array(),
            'has_analytics_data' => false,
            'can_view_analytics' => !empty($caps['select_analytics']),
        );

        if (!empty($caps['select_users'])) {
            $this->load->model('Admin/UserModel');
            $user = new UserModel();
            $result['counts']['users'] = $user->dashboard_users_count();
            $users = $user->dashboard_users(8);
            $result['users'] = $this->dashboard_project_users($users);
        }

        if (!empty($caps['select_pages'])) {
            $this->load->model('Admin/PageModel');
            $page = new PageModel();
            $result['counts']['pages'] = $page->get_count_all(array('status_in' => array(1, 2, 3)));
            $drafts = $page->dashboard_cards(
                array('status' => '2'),
                array(5),
                array('date_update', 'DESC')
            );
            $result['pages'] = $this->dashboard_project_pages($drafts, false);

            $timeline = $page->dashboard_cards(
                array(
                    'user_id' => userdata('user_id'),
                    'status' => '1',
                ),
                array(8),
                array('date_create', 'DESC')
            );
            $result['timeline'] = $this->dashboard_project_pages($timeline, true);
        }

        if (!empty($caps['select_files'])) {
            $this->load->model('Admin/FileModel');
            $file = new FileModel();
            $result['counts']['files'] = $file->get_count_all(array('status' => 1));
            $files = $file->all(array(12), array('file_id', 'DESC'));
            $result['files'] = $this->dashboard_project_files($files);
        }

        if (!empty($caps['select_gallery'])) {
            $this->load->model('Admin/AlbumModel');
            $album = new AlbumModel();
            $result['counts']['albumes'] = $album->get_count_all(array('status' => 1));
            $albumes = $album->dashboard_albums(6);
            $result['albumes'] = $this->dashboard_project_albums($albumes);
        }

        if (!empty($caps['select_events'])) {
            $this->load->model('Admin/EventModel');
            $event = new EventModel();
            $result['counts']['events'] = $event->get_count_all(array('status' => 1));
            $upcoming = $event->upcoming(6);
            $result['events'] = $this->dashboard_project_events($upcoming);
        }

        if (!empty($caps['select_config'])) {
            $theme = config('THEME_PATH');
            if (!$theme && defined('SITE_THEME')) {
                $theme = SITE_THEME;
            }
            $result['site'] = array(
                'title' => (string) config('SITE_TITLE'),
                'tracking' => config('SITEM_TRACK_VISITORS') == 'Si',
                'theme' => (string) $theme,
            );
        }

        if (!empty($caps['select_content_data'])) {
            $this->load->model('Admin/CustomModelContentModel');
            $Form_conten = new CustomModelContentModel();
            $result['counts']['content'] = $Form_conten->get_count_all(array('status_in' => array(1, 2, 3)));
            $content = $Form_conten->dashboard_recent(5);
            $result['content'] = $this->dashboard_project_content($content);
        }

        if (!empty($caps['select_form_customs'])) {
            $this->load->model('Admin/CustomModelModel');
            $form = new CustomModelModel();
            $types = $form->all(array(20), array('custom_model_id', 'DESC'));
            $result['forms_types'] = $this->dashboard_project_forms($types);
            $result['collections'] = $result['forms_types'];
        }

        if (!empty($caps['select_analytics'])) {
            $this->load->model('Admin/UserTrackingModelEnhanced', 'analytics_model');
            $snapshot = $this->analytics_model->get_home_snapshot(30);
            $analytics_keys = array(
                'kpis', 'stats', 'chart1', 'chart2', 'chart3', 'chart4',
                'topPages', 'referrers', 'has_data',
            );
            foreach ($analytics_keys as $key) {
                if (isset($snapshot[$key])) {
                    $result[$key] = $snapshot[$key];
                }
            }
            $result['has_analytics_data'] = !empty($snapshot['has_data']);
            $result['can_view_analytics'] = true;
        }

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->cache->save($cache_key, $response, 300);
        $this->response($this->dashboard_with_layout($response), REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/v1/dashboard/layout
     * Body: { "layout": { "v": 2, "rows": [ { "cols": [ { "w": 12, "items": ["kpis"] } ] } ] } }
     * Also accepts the old flat list. Widths are 4/6/12; unknown ids are dropped.
     */
    public function layout_post()
    {
        if (!function_exists('has_permisions') || !has_permisions('UPDATE_DASHBOARD_LAYOUT')) {
            $this->response(array(
                'code' => REST_Controller::HTTP_FORBIDDEN,
                'error_message' => lang('dashboard_layout_forbidden'),
            ), REST_Controller::HTTP_FORBIDDEN);
            return;
        }
        $items = $this->dashboard_posted_layout();
        $normalized = dashboard_normalize_layout($items);
        $this->load->model('Admin/DashboardLayoutModel');
        $model = new DashboardLayoutModel();
        $ok = $model->save_for_user(userdata('user_id'), dashboard_layout_slim($normalized));
        if (!$ok) {
            $this->response_error(lang('dashboard_save_error'), array(), REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        system_logger('dashboard', userdata('user_id'), 'layout', 'Dashboard layout updated');
        $this->response_ok(dashboard_layout_payload());
    }

    /**
     * POST /api/v1/dashboard/layout_reset
     */
    public function layout_reset_post()
    {
        if (!function_exists('has_permisions') || !has_permisions('UPDATE_DASHBOARD_LAYOUT')) {
            $this->response(array(
                'code' => REST_Controller::HTTP_FORBIDDEN,
                'error_message' => lang('dashboard_layout_forbidden'),
            ), REST_Controller::HTTP_FORBIDDEN);
            return;
        }
        $this->load->model('Admin/DashboardLayoutModel');
        $model = new DashboardLayoutModel();
        $model->delete_for_user(userdata('user_id'));
        system_logger('dashboard', userdata('user_id'), 'layout', 'Dashboard layout reset');
        $this->response_ok(dashboard_layout_payload());
    }

    /**
     * @param array $response
     * @return array
     */
    private function dashboard_with_layout($response)
    {
        if (!is_array($response)) {
            $response = array('code' => 200, 'data' => array());
        }
        if (!isset($response['data']) || !is_array($response['data'])) {
            $response['data'] = array();
        }
        $payload = dashboard_layout_payload();
        $response['data']['layout'] = $payload['layout'];
        $response['data']['catalog'] = $payload['catalog'];
        $response['data']['layout_source'] = $payload['source'];
        $response['data']['can_edit_layout'] = !empty($payload['can_edit_layout']);
        if (!isset($response['data']['capabilities']) || !is_array($response['data']['capabilities'])) {
            $response['data']['capabilities'] = array();
        }
        $response['data']['capabilities']['can_edit_layout'] = !empty($payload['can_edit_layout']);
        return $response;
    }

    /**
     * @return array
     */
    private function dashboard_posted_layout()
    {
        $items = $this->post('layout');
        if ($items === null || $items === false || is_string($items) || is_object($items)) {
            $raw = $this->input->raw_input_stream;
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['layout'])) {
                $items = $decoded['layout'];
            } elseif (is_array($decoded) && (isset($decoded['rows']) || isset($decoded[0]))) {
                $items = $decoded;
            }
        }
        if (is_object($items)) {
            $items = json_decode(json_encode($items), true);
        }
        return is_array($items) ? $items : array();
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function dashboard_list($value)
    {
        if ($value === false || $value === null) {
            return array();
        }
        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }
        return is_array($value) ? $value : array();
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function dashboard_assoc($value)
    {
        if (is_object($value)) {
            return json_decode(json_encode($value), true);
        }
        return is_array($value) ? $value : array();
    }

    /**
     * @param mixed $user
     * @return array
     */
    private function dashboard_user_card($user)
    {
        $user = $this->dashboard_assoc($user);
        if (empty($user)) {
            return array();
        }
        $data = isset($user['user_data']) ? $user['user_data'] : array();
        if (!is_array($data)) {
            $data = $this->dashboard_assoc($data);
        }
        return array(
            'user_id' => isset($user['user_id']) ? $user['user_id'] : '',
            'username' => isset($user['username']) ? $user['username'] : '',
            'role' => isset($user['role']) ? $user['role'] : '',
            'avatar' => isset($data['avatar']) ? $data['avatar'] : '',
            'user_data' => array(
                'nombre' => isset($data['nombre']) ? $data['nombre'] : '',
                'apellido' => isset($data['apellido']) ? $data['apellido'] : '',
                'avatar' => isset($data['avatar']) ? $data['avatar'] : '',
            ),
        );
    }

    /**
     * @param mixed $events
     * @return array
     */
    private function dashboard_project_events($events)
    {
        $out = array();
        foreach ($this->dashboard_list($events) as $event) {
            $event = $this->dashboard_assoc($event);
            $id = isset($event['event_id']) ? (int) $event['event_id'] : 0;
            if (!$id) {
                continue;
            }
            $out[] = array(
                'event_id' => $id,
                'name' => isset($event['name']) ? $event['name'] : '',
                'date_start' => isset($event['date_start']) ? $event['date_start'] : '',
                'link' => base_url('admin/events/edit/' . $id),
            );
        }
        return $out;
    }

    /**
     * @param mixed $users
     * @return array
     */
    private function dashboard_project_users($users)
    {
        $out = array();
        foreach ($this->dashboard_list($users) as $user) {
            $card = $this->dashboard_user_card($user);
            if (!empty($card['user_id'])) {
                $out[] = $card;
            }
        }
        return $out;
    }

    /**
     * @param string $text
     * @param int $max
     * @return string
     */
    private function dashboard_excerpt($text, $max = 280)
    {
        $text = is_string($text) ? $text : '';
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($text) <= $max) {
                return $text;
            }
            return mb_substr($text, 0, $max);
        }
        if (strlen($text) <= $max) {
            return $text;
        }
        return substr($text, 0, $max);
    }

    /**
     * @param mixed $pages
     * @param bool $with_body
     * @return array
     */
    private function dashboard_project_pages($pages, $with_body)
    {
        $out = array();
        foreach ($this->dashboard_list($pages) as $page) {
            $page = $this->dashboard_assoc($page);
            $row = array(
                'page_id' => isset($page['page_id']) ? $page['page_id'] : '',
                'title' => isset($page['title']) ? $page['title'] : '',
                'status' => isset($page['status']) ? $page['status'] : '',
            );
            if ($with_body) {
                $image = isset($page['imagen_file']) ? $this->dashboard_assoc($page['imagen_file']) : array();
                $row['content'] = $this->dashboard_excerpt(isset($page['content']) ? $page['content'] : '');
                $row['date_create'] = isset($page['date_create']) ? $page['date_create'] : '';
                $row['user'] = $this->dashboard_user_card(isset($page['user']) ? $page['user'] : array());
                $row['imagen_file'] = empty($image) ? null : array(
                    'file_front_path' => isset($image['file_front_path']) ? $image['file_front_path'] : '',
                );
            }
            $out[] = $row;
        }
        return $out;
    }

    /**
     * @param mixed $files
     * @return array
     */
    private function dashboard_project_files($files)
    {
        $out = array();
        $keys = array(
            'file_id', 'file_name', 'file_path', 'file_type', 'featured',
            'date_create', 'share_link', 'status', 'user_id',
        );
        foreach ($this->dashboard_list($files) as $file) {
            $file = $this->dashboard_assoc($file);
            $row = array();
            foreach ($keys as $key) {
                $row[$key] = isset($file[$key]) ? $file[$key] : '';
            }
            $out[] = $row;
        }
        return $out;
    }

    /**
     * @param mixed $albumes
     * @return array
     */
    private function dashboard_project_albums($albumes)
    {
        $out = array();
        foreach ($this->dashboard_list($albumes) as $album) {
            $album = $this->dashboard_assoc($album);
            $items_in = isset($album['items']) ? $album['items'] : array();
            if (!is_array($items_in)) {
                $items_in = $this->dashboard_list($items_in);
            }
            $items = array();
            foreach (array_slice($items_in, 0, 2) as $item) {
                $item = $this->dashboard_assoc($item);
                $file = isset($item['file']) ? $this->dashboard_assoc($item['file']) : array();
                $items[] = array(
                    'file' => array(
                        'file_front_path' => isset($file['file_front_path']) ? $file['file_front_path'] : '',
                    ),
                );
            }
            $out[] = array(
                'album_id' => isset($album['album_id']) ? $album['album_id'] : '',
                'name' => isset($album['name']) ? $album['name'] : '',
                'items' => $items,
            );
        }
        return $out;
    }

    /**
     * @param mixed $content
     * @return array
     */
    private function dashboard_project_content($content)
    {
        $out = array();
        foreach ($this->dashboard_list($content) as $item) {
            $item = $this->dashboard_assoc($item);
            $model = isset($item['custom_model']) ? $this->dashboard_assoc($item['custom_model']) : array();
            $out[] = array(
                'custom_model_content_id' => isset($item['custom_model_content_id']) ? $item['custom_model_content_id'] : '',
                'title' => isset($item['title']) ? $item['title'] : '',
                'status' => isset($item['status']) ? $item['status'] : '',
                'date_create' => isset($item['date_create']) ? $item['date_create'] : '',
                'custom_model' => array(
                    'form_name' => isset($model['form_name']) ? $model['form_name'] : '',
                ),
                'user' => $this->dashboard_user_card(isset($item['user']) ? $item['user'] : array()),
            );
        }
        return $out;
    }

    /**
     * @param mixed $types
     * @return array
     */
    private function dashboard_project_forms($types)
    {
        $out = array();
        foreach ($this->dashboard_list($types) as $type) {
            $type = $this->dashboard_assoc($type);
            $out[] = array(
                'custom_model_id' => isset($type['custom_model_id']) ? $type['custom_model_id'] : '',
                'form_name' => isset($type['form_name']) ? $type['form_name'] : '',
            );
        }
        return $out;
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
