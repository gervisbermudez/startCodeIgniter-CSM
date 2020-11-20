<?php

require APPPATH . 'libraries/REST_Controller.php';

class Menus extends REST_Controller
{

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
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
        $this->load->model('Admin/Menu');
    }

    /**
     * @api {get} /api/v1/categorie/:menu_id Request Categorie information
     * @apiName GetCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} menu_id Categorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "menu_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "menu_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
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
    public function index_get($menu_id = null)
    {
        $menu = new Menu();
        if ($menu_id) {
            $result = $menu->find_with(array('menu_id' => $menu_id));
            $result = $result ? $menu->as_data() : [];
        } else {
            $result = $menu->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        if ($menu_id) {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => [],
                'requets_data' => $_POST,
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $this->load->library('FormValidator');
        $form = new FormValidator();

        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[1]'),
            array('field' => 'template', 'label' => 'description', 'rules' => 'required|min_length[1]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
        );

        $form->set_rules($config);

        if (!$form->run()) {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'error_message' => lang('validations_error'),
                'errors' => $form->_error_array,
                'request_data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $menu = new Menu();

        $this->input->post('menu_id') ? $menu->find($this->input->post('menu_id')) : false;
        $menu->name = $this->input->post('name');
        $menu->template = $this->input->post('template');
        $menu->position = $this->input->post('position');
        $menu->user_id = userdata('user_id');
        $menu->status = $this->input->post('status');
        $menu->date_create = date("Y-m-d H:i:s");
        $menu->date_publish = date("Y-m-d H:i:s");
        if ($menu->save()) {
            $this->load->model('Admin/Menu_items');
            $menu_item = new Menu_items();
            $menu_item->delete_data(['menu_id' => $menu->menu_id]);
            $menu_items = $this->input->post('menu_items');
            $this->save_menu_items($menu_items, $menu);
            $menu->find($menu->menu_id);
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $menu,
            );
            $this->response($response, REST_Controller::HTTP_OK);
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                "error_message" => lang('unexpected_error'),
                'data' => $_POST,
                'request_data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    private function save_menu_items($menu_items, $menu, $parent_id = 0)
    {
        foreach ($menu_items as $key => $item) {
            $item = (object) $item;
            $menu_item = new Menu_items();
            isset($item->menu_item_id) ? $menu_item->find($item->menu_item_id) : false;
            $menu_item->menu_id = $menu->menu_id;
            $menu_item->menu_item_parent_id = $parent_id;
            $menu_item->item_type = $item->item_type;
            $menu_item->order = $item->order;
            $menu_item->model_id = isset($item->model_id) ? $item->model_id : null;
            $menu_item->model = isset($item->model) ? $item->model : null;
            $menu_item->item_name = $item->item_name;
            $menu_item->item_label = $item->item_label;
            $menu_item->item_link = $item->item_link;
            $menu_item->item_title = $item->item_title;
            $menu_item->item_target = $item->item_target;
            $menu_item->status = $item->status;
            $menu_item->date_create = $item->date_create;
            $menu_item->date_publish = $item->date_publish;
            $menu_item->save();

            if (isset($item->subitems) && count($item->subitems) > 0) {
                $this->save_menu_items($item->subitems, $menu, $menu_item->menu_item_id);
            }
        }
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
    public function index_delete($menu_id = null)
    {
        if ($menu_id) {
            $menu = new Menu();
            $result = $menu->find($menu_id);
            if ($result) {
                $response = array(
                    'code' => REST_Controller::HTTP_OK,
                    'data' => [
                        "result" => $menu->delete(),
                    ],
                );
            } else {
                $response = array(
                    'code' => REST_Controller::HTTP_NOT_FOUND,
                    "error_message" => lang('not_found_error'),
                    'data' => [],
                );
            }
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * @api {get} /api/v1/categorie/subcategorie/:menu_id/:submenu_id Request SubCategorie information
     * @apiName GetSubCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} menu_id Categorie unique ID.
     * @apiParam {Number} submenu_id SubCategorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "menu_id": "4",
     *               "name": "SubCategoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "menu_id": "5",
     *               "name": "SubCategoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
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
    public function subcategorie_get($menu_id, $submenu_id = null)
    {
        $menu = new Menu();
        if ($submenu_id) {
            $result = $menu->where(array('menu_item_parent_id' => $menu_id, 'menu_id' => $submenu_id));
            $result = $result ? $result->first() : [];
        } else {
            $result = $menu->where(array('menu_item_parent_id' => $menu_id));
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        if ($menu_id) {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => [],
            );
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function subcategorie_post()
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function subcategorie_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function subcategorie_delete($id = null)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * @api {get} /api/v1/categorie/type/:type/ Request Categorie information
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
     *               "menu_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "menu_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
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
    public function type_get($type = 0)
    {
        $menu = new Menu();
        $result = $menu->where(array('menu_item_parent_id' => '0', 'type' => $type));

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => [],
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * @api {get} /api/v1/categorie/type/:type/ Request Categorie information
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
     *               "menu_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "menu_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
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

        $menu = new Menu();
        $result = $menu->where(
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

    public function templates_get()
    {
        $this->load->helper('directory');
        $directory = APPPATH . 'views\\site\\templates\\menu';
        if (getThemePath()) {
            $directory = getThemePath() . 'views\\site\\templates\\menu';
        }
        $map = directory_map($directory);
        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => $map,
        );
        $this->response($response, REST_Controller::HTTP_OK);

    }

}
