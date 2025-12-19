<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class FragmentsController extends REST_Controller
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
        $this->load->model('Admin/FragmentModel');

    }

    /**
     * @api {get} /api/v1/categorie/:fragment_id Request Fragments information
     * @apiName GetCategorie
     * @apiGroup Fragments
     *
     * @apiParam {Number} fragment_id Fragments unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "fragment_id": "4",
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
     *               "fragment_id": "5",
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
    public function index_get($fragment_id = null)
    {
        $fragmento = new FragmentModel();
        if ($fragment_id) {
            $result = $fragmento->where(array('fragment_id' => $fragment_id));
            $result = $result ? $result->first() : [];
        } else {
            $result = $fragmento->where(array());
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
            array('field' => 'description', 'label' => 'description', 'rules' => 'required|min_length[1]'),
            array('field' => 'type', 'label' => 'type', 'rules' => 'required|min_length[1]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
        );

        $form->set_rules($config);

        if (!$form->run()) {
            $this->response_error(lang('validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Obtener datos validados
        $fragment_id = $this->input->post('fragment_id', TRUE);
        $name = $this->input->post('name', TRUE);
        $description = $this->input->post('description', TRUE);
        $type = $this->input->post('type', TRUE);
        $status = (int)$this->input->post('status');

        $fragmento = new FragmentModel();

        if ($fragment_id) {
            $fragmento->find($fragment_id);
        }

        $fragmento->name = $name;
        $fragmento->description = $description;
        $fragmento->user_id = userdata('user_id');
        $fragmento->type = $type;
        $fragmento->status = $status;
        $fragmento->date_create = date("Y-m-d H:i:s");
        $fragmento->date_publish = date("Y-m-d H:i:s");
        if ($fragmento->save()) {
            $this->response_ok($fragmento);
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
    public function index_delete($fragment_id = null)
    {
        $fragmento = new FragmentModel();
        $result = $fragmento->find($fragment_id);
        if ($result) {
            $result = $fragmento->delete($fragment_id);
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

}
