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
     * @api {get} /api/v1/fragments/:fragment_id Request fragment information
     * @apiName GetFragment
     * @apiGroup Fragments
     *
     * @apiParam {Number} [fragment_id] Fragment unique ID. Omit for the list (published + draft).
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "fragment_id": "17",
     *               "name": "about_me",
     *               "description": "<p>HTML content</p>",
     *               "type": "contenido",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           }
     *       ]
     *   }
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
        if (!$this->require_fragment_permision('SELECT_FRAGMENTS')) {
            return;
        }

        $fragmento = new FragmentModel();
        if ($fragment_id) {
            $result = $fragmento->where(array('fragment_id' => $fragment_id));
            $result = $result ? $result->first() : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $status = $this->get('status');
        $type = $this->get('type');
        $where = array('status_in' => array(1, 2));
        $options = array('unfiltered' => true);
        if ($status !== null && $status !== '') {
            $where = array('status' => (int) $status);
            $options = array();
        }
        $allowed_types = $this->allowed_fragment_types();
        if ($type !== null && $type !== '' && in_array($type, $allowed_types, true)) {
            $where['type'] = $type;
        }
        $this->respond_index_list($fragmento, $where, array(), $options);
    }

    /**
     * Create or update a fragment. description is HTML (no xss_clean).
     *
     * @return Response
     */
    public function index_post()
    {
        $fragment_id = $this->input->post('fragment_id', TRUE);
        $is_update = ($fragment_id !== null && $fragment_id !== '' && $fragment_id !== false);
        if (!$this->require_fragment_permision($is_update ? 'UPDATE_FRAGMENT' : 'CREATE_FRAGMENT')) {
            return;
        }

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

        $name = $this->input->post('name', TRUE);
        $description = $this->input->post('description');
        $type = $this->input->post('type', TRUE);
        $status = (int) $this->input->post('status');
        if (!in_array($type, $this->allowed_fragment_types(), true)) {
            $this->response_error(
                lang('validations_error'),
                array('errors' => array('type' => 'The type field is invalid.')),
                REST_Controller::HTTP_BAD_REQUEST,
                REST_Controller::HTTP_BAD_REQUEST
            );
            return;
        }

        if ($this->fragment_name_taken($name, $is_update ? $fragment_id : null)) {
            $this->response_error(
                lang('validations_error'),
                array('errors' => array('name' => 'The name field must contain a unique value.')),
                REST_Controller::HTTP_BAD_REQUEST,
                REST_Controller::HTTP_BAD_REQUEST
            );
            return;
        }

        $fragmento = new FragmentModel();
        $old_name = '';

        if ($is_update) {
            if (!$fragmento->find($fragment_id)) {
                $this->response_error(lang('not_found_error'));
                return;
            }
            $old_name = $fragmento->name;
        }

        $fragmento->name = $name;
        $fragmento->description = $description;
        $fragmento->type = $type;
        $fragmento->status = $status;
        $fragmento->date_update = date("Y-m-d H:i:s");

        if (!$is_update) {
            $fragmento->user_id = userdata('user_id');
            $fragmento->date_create = date("Y-m-d H:i:s");
        }

        if ($fragmento->save()) {
            bust_fragment_cache($old_name);
            bust_fragment_cache($fragmento->name);
            system_logger(
                'fragments',
                $fragmento->fragment_id,
                $is_update ? 'updated' : 'created',
                $is_update ? 'A fragment has been updated' : 'A fragment has been created'
            );
            $this->response_ok($fragmento);
            return;
        }

        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
    }

    /**
     * Unique name among non-deleted rows. Exclude self on update.
     *
     * @param string $name
     * @param mixed $exclude_fragment_id
     * @return bool
     */
    protected function fragment_name_taken($name, $exclude_fragment_id = null)
    {
        $this->db->where('name', $name);
        $this->db->where('status !=', 0);
        if ($exclude_fragment_id !== null && $exclude_fragment_id !== '' && $exclude_fragment_id !== false) {
            $this->db->where('fragment_id !=', $exclude_fragment_id);
        }
        $this->db->limit(1);
        return $this->db->get('fragmentos')->num_rows() > 0;
    }

    /**
     * @return Response
     */
    public function index_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);

    }

    /**
     * Soft-delete a fragment and bust its embed cache.
     *
     * @return Response
     */
    public function index_delete($fragment_id = null)
    {
        if (!$this->require_fragment_permision('DELETE_FRAGMENT')) {
            return;
        }

        $fragmento = new FragmentModel();
        if (!$fragmento->find($fragment_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        bust_fragment_cache($fragmento->name);

        if ($fragmento->delete($fragment_id)) {
            system_logger('fragments', $fragmento->fragment_id, 'deleted', 'A fragment has been deleted');
            $this->response_ok($fragmento);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

    /**
     * Editorial labels stored on fragmentos.type. Not used by fragment().
     *
     * @return array
     */
    protected function allowed_fragment_types()
    {
        return array(
            'contenido',
            'parrafo',
            'widget',
            'page',
            'formulario',
            'video',
            'foto',
            'evento',
        );
    }

    /**
     * @param string $permision
     * @return bool
     */
    protected function require_fragment_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }

}
