<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class Dashboard extends REST_Controller
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
        $this->load->model('Admin/Categorie');
        $this->load->model('Admin/User');
        $this->load->model('Admin/Page');
        $this->load->model('Admin/CustomModel');
        $this->load->model('Admin/CustomModelContent');
        $this->load->model('Admin/File');
        $this->load->model('Admin/Album');

        $result = array();

        $Form_conten = new CustomModelContent();
        $result['content'] = $Form_conten->all();

        $form = new CustomModel();
        $result['forms_types'] = $form->all();

        $dashboard = new Categorie();
        $result['dashboards'] = $dashboard->where(array('parent_id' => '0'));

        $user = new User();
        $result['users'] = $user->get_full_info();
        $result['timeline'] = $user->get_timeline(userdata('user_id'));

        $page = new Page();
        $result['pages'] = $page->where(["status !=" => "0"]);

        $file = new File();
        $result['files'] = $file->all();

        $album = new Album();
        $result['albumes'] = $album->all();

        $this->load->model('Admin/UserTracking');
        $User_tracking = new UserTracking();
        $tempResult = $User_tracking->all();
        $tempData = $tempResult ? $tempResult->toArray() : [];

        $result['chart1'] = $this->generateTrafficChart($tempData);
        $result['chart2'] = $this->getRequestByMont($tempData);
        $result['chart3'] = $this->getTraficByDevice($tempData);
        $result['chart4'] = $this->getTopVisitedUrls($tempData, 7);

        $response = array(
            'code' => 200,
            'data' => $result,
        );

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
            // Obtener el mes de la fecha del objeto de entrada
            $month = date('F', strtotime($entry->date_create));

            // Si ya se contaron visitas para este mes, agregar una al recuento existente
            if (isset($visits[$month])) {
                $visits[$month]++;
            }
            // De lo contrario, crear una nueva entrada en el array de recuento de visitas
            else {
                $visits[$month] = 1;
            }
        }

        // Eliminar los meses sin visitas del array de datos
        $visits = array_filter($visits);

        // Ordenar los datos por mes (orden alfabético)
        ksort($visits);

        // Crear arrays separados para las etiquetas (meses) y los datos (número de visitas)
        foreach ($visits as $month => $count) {
            $labels[] = $month;
            $data[] = $count;
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
        $ignoredUrls = ['/favicon.ico', '/robots.txt', '/'];
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
            'otros' => 0,
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

        $dashboard = new Categorie();
        $result = $dashboard->where(
            $_GET
        );

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
                'filters' => $_GET
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
        $this->load->model('Admin/Notifications');

        $Notifications = new Notifications();
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
        $this->load->model('Admin/Notifications');

        $Notifications = new Notifications();
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
