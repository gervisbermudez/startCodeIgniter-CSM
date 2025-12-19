<?php

require APPPATH . 'libraries/REST_Controller.php';

class NotesController extends REST_Controller
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
        /* print_r("here");
        die();
        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        } */

        $this->load->database();
        $this->load->model('Admin/NoteModel');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($note_id = null)
    {
        $note = new NoteModel();
        if ($note_id) {
            $result = $note->find($note_id);
            $result = $result ? $note : [];
        } else {
            $result = $note->where(['status' => '1']);
            $archived = $note->where(['status' => '2']);
            $result = $result ? $result->toArray() : [];
            $archived = $archived ? $archived->toArray() : [];
            $result = array_merge($result, $archived);
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        if ($note_id) {
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
    public function index_post()
    {
        $this->load->library('FormValidator');
        $form = new FormValidator();

        $config = array(
            array('field' => 'title', 'label' => 'title', 'rules' => 'required|min_length[5]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'required|min_length[5]'),
            array('field' => 'categorie_id', 'label' => 'categorie_id', 'rules' => 'integer|is_natural'),
            array('field' => 'subcategorie_id', 'label' => 'subcategorie_id', 'rules' => 'integer|is_natural'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer|is_natural_no_zero'),
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

        $note = new NoteModel();
        $this->input->post('note_id') ? $note->find($this->input->post('note_id')) : false;
        $note->user_id = userdata('user_id');
        $note->title = $this->input->post('title');
        $note->content = $this->input->post('content');
        $note->json_content = $this->input->post('json_content');
        $note->tags = $this->input->post('tags');
        $note->attachments = $this->input->post('attachments');
        $note->type = $this->input->post('type');
        $note->selected_color = $this->input->post('selected_color');
        $note->status = $this->input->post('status');
        $note->categorie_id = $this->input->post('categorie_id');
        $note->subcategorie_id = $this->input->post('subcategorie_id');
        $note->date_create = date("Y-m-d H:i:s");

        if ($note->save()) {
            system_logger('notes', $note->note_id, ($this->input->post('note_id') ? "updated" : "created"), ($this->input->post('note_id') ? "A note has been updated" : "A note has been created"));
            $this->response_ok($note);

        } else {
            $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST);
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
    public function index_delete($id = null)
    {
        if (!$id) {
            $this->response_error(lang('not_found_error'));
        }

        $note = new NoteModel();
        $note->find($id);

        if ($note->delete()) {
            system_logger('notes', $note->note_id, ("deleted"), ("A note has been deleted"));
            $this->response_ok($note);
        } else {
            $this->response_error(lang('not_found_error'));
        }
    }
}
