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
        // Intentar obtener datos del caché
        $cache_key = 'dashboard_data_' . userdata('user_id');
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

        $this->load->model('Admin/UserTrackingModel');
        $User_tracking = new UserTrackingModel();
        $tempResult = $User_tracking->all();
        $tempData = $tempResult ? $tempResult->toArray() : [];

        $result['chart1'] = $this->generateTrafficChart($tempData);
        $result['chart2'] = $this->getRequestByMont($tempData);
        $result['chart3'] = $this->getTraficByDevice($tempData);
        $result['chart4'] = $this->getTopVisitedUrls($tempData, 7);

        // Calcular estadísticas generales
        $result['stats'] = $this->calculateStats($tempData);
        $result['kpis'] = $this->calculateKPIs($tempData);
        $result['referrers'] = $this->getTopReferrers($tempData, 5);
        $result['topPages'] = $this->getTopPages($tempData, 5);
        $result['hourlyHeatmap'] = $this->getHourlyHeatmap($tempData);

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        // Guardar en caché por 5 minutos (300 segundos)
        $this->cache->save($cache_key, $response, 300);

        $this->response($response, REST_Controller::HTTP_OK);
    }

    private function getRequestByMont($tempData)
    {

        // Recopilar los datos del objeto de entrada en un array

        // Inicializar arrays para las etiquetas (meses) y los datos (número de visitas)
        $labels = array();
        $data = array();
        $visits = array();

        // Iterar a través de los datos y contar las visitas por mes
        foreach ($tempData as $entry) {
            // Obtener el mes y año de la fecha del objeto de entrada
            $monthYear = date('Y-m', strtotime($entry->date_create));
            $month = date('F', strtotime($entry->date_create));

            // Si ya se contaron visitas para este mes, agregar una al recuento existente
            if (isset($visits[$monthYear])) {
                $visits[$monthYear]['count']++;
            }
            // De lo contrario, crear una nueva entrada en el array de recuento de visitas
            else {
                $visits[$monthYear] = array(
                    'count' => 1,
                    'label' => $month
                );
            }
        }

        // Eliminar los meses sin visitas del array de datos
        $visits = array_filter($visits);

        // Ordenar los datos por mes-año cronológicamente
        ksort($visits);

        // Crear arrays separados para las etiquetas (meses) y los datos (número de visitas)
        foreach ($visits as $monthData) {
            $labels[] = $monthData['label'];
            $data[] = $monthData['count'];
        }

        // Crear el array de salida en el formato deseado
        $output = array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    "tension" => 0.5,
                    'data' => $data,
                ),
            ),
        );

        return $output;
    }

    private function getTopVisitedUrls($data, $topCount = 5)
    {
        $ignoredUrls = [
            '/favicon.ico', 
            '/robots.txt', 
            '/', 
            '/sitemap.xml',
            '/manifest.json',
            '/service-worker.js',
            '/sw.js',
            '/apple-touch-icon.png',
            '/browserconfig.xml'
        ];
        $filteredData = array_filter($data, function ($item) use ($ignoredUrls) {
            return !in_array($item->requested_url, $ignoredUrls);
        });

        // Obtener un array con la cuenta de visitas de cada requested_url
        $visitsCount = array_count_values(array_column($filteredData, 'requested_url'));

        // Ordenar el array de mayor a menor visitas
        arsort($visitsCount);

        // Tomar los primeros $topCount elementos del array
        $topUrls = array_slice($visitsCount, 0, $topCount);

        // Crear los arrays para la salida del gráfico
        $labels = array_keys($topUrls);
        $data = array_values($topUrls);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                ],
            ],
        ];
    }

    private function generateTrafficChart($data)
    {
        $visits = array();
        foreach ($data as $item) {
            $date = new DateTime($item->date_create);
            $day = $date->format('M j');
            $month = $date->format('M');

            if (!isset($visits[$month])) {
                $visits[$month] = array();
            }

            if (!isset($visits[$month][$day])) {
                $visits[$month][$day] = 0;
            }
            $visits[$month][$day]++;
        }

        $topDays = array();
        foreach ($visits as $month => $monthVisits) {
            arsort($monthVisits);
            $topDays[$month] = array_slice($monthVisits, 0, 5, true);
        }

        $labels = array();
        $chartData = array();

        foreach ($topDays as $month => $monthTopDays) {
            foreach ($monthTopDays as $day => $visitsCount) {
                $labels[] = "$day";
                $chartData[] = $visitsCount;
            }
        }

        $chart = array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    "tension" => 0.5,
                    'data' => $chartData,
                ),
            ),
        );

        return $chart;
    }

    private function getTraficByDevice($data)
    {
        $this->load->library('user_agent');

        // array para almacenar la cantidad de visitas por dispositivo
        $visitas_por_dispositivo = array(
            'smartphone' => 0,
            'tablet' => 0,
            'desktop' => 0,
            'others' => 0,
            'robot' => 0,
        );

        $Parser = new CI_User_agent();

        // recorrer las visitas
        foreach ($data as $visita) {
            // obtener el user_agent de la visita

            $user_agent = $visita->user_agent;
            //$this->agent->parse($user_agent);
            $Parser->parse($user_agent);
            // detectar el tipo de dispositivo
            if ($Parser->is_mobile()) {
                $visitas_por_dispositivo['smartphone']++;
            } else if ($Parser->is_robot()) {
                $visitas_por_dispositivo['robot']++;
            } else {
                $visitas_por_dispositivo['desktop']++;
            }
        }

        // generar el gráfico
        $data = array(
            'labels' => array_keys($visitas_por_dispositivo),
            'datasets' => array(
                array(
                    'data' => array_values($visitas_por_dispositivo),
                ),
            ),
        );

        return $data;
    }

    private function calculateStats($data)
    {
        $totalVisitors = count($data);
        $uniqueIPs = array_unique(array_column($data, 'ip_address'));
        $uniqueVisitors = count($uniqueIPs);
        
        // Calcular crecimiento comparando últimos 7 días vs 7 días anteriores
        $now = time();
        $lastWeek = [];
        $previousWeek = [];
        
        foreach ($data as $item) {
            $timestamp = strtotime($item->date_create);
            $daysAgo = floor(($now - $timestamp) / 86400);
            
            if ($daysAgo <= 7) {
                $lastWeek[] = $item;
            } elseif ($daysAgo > 7 && $daysAgo <= 14) {
                $previousWeek[] = $item;
            }
        }
        
        $lastWeekCount = count($lastWeek);
        $previousWeekCount = count($previousWeek) ?: 1;
        $visitorGrowth = round((($lastWeekCount - $previousWeekCount) / $previousWeekCount) * 100);
        
        return array(
            'totalVisitors' => $uniqueVisitors,
            'visitorGrowth' => $visitorGrowth,
            'totalRequests' => $totalVisitors,
            'requestGrowth' => $visitorGrowth
        );
    }

    private function calculateKPIs($data)
    {
        $totalVisits = count($data);
        $uniqueIPs = array_unique(array_column($data, 'ip_address'));
        $uniqueVisitors = count($uniqueIPs);
        
        // Calcular páginas por sesión (promedio)
        $pagesPerSession = $uniqueVisitors > 0 ? round($totalVisits / $uniqueVisitors, 2) : 0;
        
        // Calcular bounce rate (visitantes con solo 1 página vista)
        $ipCounts = array_count_values(array_column($data, 'ip_address'));
        $singlePageVisits = count(array_filter($ipCounts, function($count) { return $count == 1; }));
        $bounceRate = $uniqueVisitors > 0 ? round(($singlePageVisits / $uniqueVisitors) * 100, 1) : 0;
        
        // Visitas hoy
        $today = date('Y-m-d');
        $todayVisits = count(array_filter($data, function($item) use ($today) {
            return date('Y-m-d', strtotime($item->date_create)) == $today;
        }));
        
        // Visitas ayer para comparación
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $yesterdayVisits = count(array_filter($data, function($item) use ($yesterday) {
            return date('Y-m-d', strtotime($item->date_create)) == $yesterday;
        }));
        
        $dailyGrowth = $yesterdayVisits > 0 ? round((($todayVisits - $yesterdayVisits) / $yesterdayVisits) * 100, 1) : 0;
        
        return array(
            'uniqueVisitors' => $uniqueVisitors,
            'totalVisits' => $totalVisits,
            'pagesPerSession' => $pagesPerSession,
            'bounceRate' => $bounceRate,
            'todayVisits' => $todayVisits,
            'yesterdayVisits' => $yesterdayVisits,
            'dailyGrowth' => $dailyGrowth
        );
    }
    
    private function getTopReferrers($data, $topCount = 5)
    {
        $referrers = array();
        
        foreach ($data as $item) {
            if (isset($item->referer) && !empty($item->referer) && $item->referer != '-') {
                $host = parse_url($item->referer, PHP_URL_HOST);
                if ($host) {
                    $referrers[] = $host;
                }
            } else {
                $referrers[] = 'Direct';
            }
        }
        
        $referrerCounts = array_count_values($referrers);
        arsort($referrerCounts);
        $topReferrers = array_slice($referrerCounts, 0, $topCount, true);
        
        return array(
            'labels' => array_keys($topReferrers),
            'datasets' => array(
                array('data' => array_values($topReferrers))
            )
        );
    }
    
    private function getTopPages($data, $topCount = 5)
    {
        $ignoredUrls = [
            '/favicon.ico', '/robots.txt', '/', '/sitemap.xml',
            '/manifest.json', '/service-worker.js', '/sw.js'
        ];
        
        $pages = array();
        foreach ($data as $item) {
            if (!in_array($item->requested_url, $ignoredUrls)) {
                $pages[] = $item->requested_url;
            }
        }
        
        $pageCounts = array_count_values($pages);
        arsort($pageCounts);
        
        return array_slice($pageCounts, 0, $topCount, true);
    }
    
    private function getHourlyHeatmap($data)
    {
        $heatmap = array();
        $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
        
        // Inicializar matriz
        foreach ($days as $day) {
            for ($hour = 0; $hour < 24; $hour++) {
                $heatmap[$day][$hour] = 0;
            }
        }
        
        // Llenar con datos
        foreach ($data as $item) {
            $timestamp = strtotime($item->date_create);
            $day = date('D', $timestamp);
            $hour = (int)date('G', $timestamp);
            
            if (isset($heatmap[$day][$hour])) {
                $heatmap[$day][$hour]++;
            }
        }
        
        return $heatmap;
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

        $Notifications = new NotificationsModel();
        $result = $Notifications->where(
            array(
                'status' => 1, // 1 = pendiente, 2 = leido

            )
        );

        $response = array(
            'code' => 200,
            'data' => $result,
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     *
     * @return Response
     */
    public function notifications_post($id = null)
    {
        $this->load->model('Admin/NotificationsModel');

        $Notifications = new NotificationsModel();
        $Notifications->find($id);
        if ($Notifications) {
            $Notifications->status = 2; //archive
            $Notifications->save();
            $this->response_ok($Notifications);
        } else {
            $this->response_error(lang('not_found_error'));
        }
    }

}
