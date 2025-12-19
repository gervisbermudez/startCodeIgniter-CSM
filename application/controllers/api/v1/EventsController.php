<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class EventsController extends REST_Controller
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
        $this->load->model('Admin/EventModel');
    }

    /**
     * @api {get} /api/v1/categorie/:event_id Request Categorie information
     * @apiName GetCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} event_id Categorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "event_id": "4",
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
     *               "event_id": "5",
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
    public function index_get($event_id = null)
    {
        $event = new EventModel();
        if ($event_id) {
            $result = $event->find_with(array('event_id' => $event_id));
            $result = $result ? $event->as_data() : [];
        } else {
            $result = $event->all();
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
            array('field' => 'subtitle', 'label' => 'subtitle', 'rules' => 'min_length[1]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'required|min_length[1]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
        );
        $form->set_rules($config);
        if (!$form->run()) {
            $this->response_error(lang('validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $event = new EventModel();
        $this->input->post('event_id') ? $event->find($this->input->post('event_id')) : false;
        $event->name = $this->input->post('name');
        $event->subtitle = $this->input->post('subtitle');
        $event->content = $this->input->post('content');
        $event->address = $this->input->post('address');
        $event->visibility = $this->input->post('visibility');
        $event->mainImage = $this->input->post('mainImage');
        $event->categorie_id = $this->input->post('categorie_id');
        $event->user_id = userdata('user_id');
        $event->status = $this->input->post('status');
        $event->date_create = date("Y-m-d H:i:s");
        $event->date_publish = date("Y-m-d H:i:s");
        if ($event->save()) {
            $this->response_ok($event);
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
    public function index_delete($event_id = null)
    {
        if ($event_id) {
            $event = new EventModel();
            $result = $event->find($event_id);
            if ($result) {
                $this->response_ok(["result" => $event->delete()]);
                return;
            } else {
                $this->response_error(lang('not_found_error'));
                return;
            }
        }
        $this->response_error(lang('not_found_error'));
        return;
    }

}
