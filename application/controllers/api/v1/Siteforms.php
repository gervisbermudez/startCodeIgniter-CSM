<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require APPPATH . 'libraries/REST_Controller.php';

class Siteforms extends REST_Controller
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
        $this->load->model('Admin/SiteForm');
    }

    /**
     * @api {get} /api/v1/categorie/:siteform_id Request Categorie information
     * @apiName GetCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} siteform_id Categorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "siteform_id": "4",
     *               "name": "Manu 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "siteform_id": "5",
     *               "name": "Manu 2",
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
     * @apiError ManuNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function index_get($siteform_id = null)
    {
        $siteform = new SiteForm();
        if ($siteform_id) {
            $result = $siteform->find_with(array('siteform_id' => $siteform_id));
            $result = $result ? $siteform->as_data() : [];
        } else {
            $result = $siteform->all();
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));

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
            $this->response_error(lang('validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $siteform = new SiteForm();
        $this->input->post('siteform_id') ? $siteform->find($this->input->post('siteform_id')) : false;
        $siteform->name = $this->input->post('name');
        $siteform->template = $this->input->post('template');
        $siteform->user_id = userdata('user_id');
        $siteform->status = $this->input->post('status');
        $siteform->date_create = date("Y-m-d H:i:s");
        $siteform->date_publish = date("Y-m-d H:i:s");
        if ($siteform->save()) {
            $siteform_items = $this->input->post('siteform_items');
            
            foreach ($siteform_items as $key => $item) {
                $item = (object) $item;
                $siteform_item = new siteform_items();
                isset($item->siteform_item_id) ? $siteform_item->find($item->siteform_item_id) : false;
                $siteform_item->siteform_id = $siteform->siteform_id;
                $siteform_item->order = isset($item->order) ? $item->order : '0';
                $siteform_item->item_type = $item->item_type;
                $siteform_item->item_name = $item->item_name;
                $siteform_item->item_label = $item->item_label;
                $siteform_item->item_class = $item->item_class;
                $siteform_item->item_title = $item->item_title;
                $siteform_item->item_placeholder = $item->item_placeholder;
                $siteform_item->properties = $item->properties;
                $siteform_item->data = $item->data;
                $siteform_item->status = $item->status;
                $siteform_item->date_create = $item->date_create;
                $siteform_item->date_publish = $item->date_publish;
                $siteform_item->save();
            }
            $this->response_ok($siteform);
            return;
        }
        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
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
    public function index_delete($siteform_id = null)
    {
        if ($siteform_id) {
            $siteform = new SiteForm();
            $result = $siteform->find($siteform_id);
            if ($result) {
                $this->response_ok(["result" => $siteform->delete()]);
                return;
            } else {
                $this->response_error(lang('not_found_error'));
                return;
            }
        }
        $this->response_error(lang('not_found_error'));
        return;
    }

    /**
     * @api {get} /api/v1/categorie/subcategorie/:siteform_id/:subsiteform_id Request SubCategorie information
     * @apiName GetSubCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} siteform_id Categorie unique ID.
     * @apiParam {Number} subsiteform_id SubCategorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "siteform_id": "4",
     *               "name": "SubSiteForm 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "siteform_id": "5",
     *               "name": "SubSiteForm 2",
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
    public function subcategorie_get($siteform_id, $subsiteform_id = null)
    {
        $siteform = new SiteForm();
        if ($subsiteform_id) {
            $result = $siteform->where(array('menu_item_parent_id' => $siteform_id, 'siteform_id' => $subsiteform_id));
            $result = $result ? $result->first() : [];
        } else {
            $result = $siteform->where(array('menu_item_parent_id' => $siteform_id));
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        if ($siteform_id) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $this->response_error(lang('not_found_error'));
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
     *               "siteform_id": "4",
     *               "name": "SiteForm 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "siteform_id": "5",
     *               "name": "SiteForm 2",
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
        $siteform = new SiteForm();
        $result = $siteform->where(array('menu_item_parent_id' => '0', 'type' => $type));
        if ($result) {;
            $this->response_ok($result);
            return;
        }
        $this->response_error(lang('not_found_error'));
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
     *               "siteform_id": "4",
     *               "name": "SiteForm 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "menu_item_parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "siteform_id": "5",
     *               "name": "SiteForm 2",
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
        $siteform = new SiteForm();
        $result = $siteform->where($_GET);
        if ($result) {

            $this->response_ok($result);
        }
        $this->response_error(lang('not_found_error'), ['filters' => $_GET]);
    }

    public function templates_get()
    {
        $this->load->helper('directory');
        $directory = APPPATH . '/views/site/templates/forms';
        if (getThemePath()) {
            $directory = getThemePath() . '/views/site/templates/forms';
        }
        $map = directory_map($directory);
        $this->response_ok($map);
    }

}
