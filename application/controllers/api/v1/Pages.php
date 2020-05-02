<?php

require APPPATH . 'libraries/REST_Controller.php';

class Pages extends REST_Controller
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

        if (!$this->session->userdata('logged_in')) {
            $this->lang->load('login_lang', 'english');
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
                'error_message' => lang('user_not_authenticated'),
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->model('Admin/Page');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($page_id = null)
    {
        $page = new Page();
        if ($page_id) {
            $result = $page->find($page_id);
        } else {
            $result = $page->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_NOT_FOUND,
            "error_message" => lang('not_found_error'),
            'data' => [],
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);
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
            array('field' => 'subtitle', 'label' => 'subtitle', 'rules' => 'min_length[5]'),
            array('field' => 'path', 'label' => 'path', 'rules' => 'required|min_length[5]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'required|min_length[5]'),
            array('field' => 'type', 'label' => 'type', 'rules' => 'integer|is_natural_no_zero'),
            array('field' => 'categorie', 'label' => 'categorie', 'rules' => 'integer|is_natural'),
            array('field' => 'subcategorie', 'label' => 'subcategorie', 'rules' => 'integer|is_natural'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer|is_natural_no_zero'),
            array('field' => 'visibility', 'label' => 'visibility', 'rules' => 'integer|is_natural_no_zero'),
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

        $page = new Page();
        $this->input->post('page_id') ? $page->find($this->input->post('page_id')) : false;
        $page->title = $this->input->post('title');
        $page->subtitle = $this->input->post('subtitle');
        $page->path = $this->input->post('path');
        $page->content = $this->input->post('content');
        $page->author = userdata('user_id');
        $page->type = $this->input->post('type');
        $page->status = $this->input->post('status');
        $page->template = $this->input->post('template');
        $page->layout = $this->input->post('layout');

        $page->date_publish = $this->input->post('publishondate') == 'true' ? date("Y-m-d H:i:s") : $this->input->post('date_publish');
        $page->date_create = date("Y-m-d H:i:s");

        $page->visibility = $this->input->post('visibility');
        $page->categorie = $this->input->post('categorie');
        $page->subcategories = $this->input->post('subcategorie');

        if ($page->save()) {

            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $page,
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
        $page = new Page();
        $page->find($id);
        if ($page->delete()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $page,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'error_message' => lang('not_found_error'),
                'data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function types_get()
    {
        $this->load->model('Admin/Page_types');

        $page_types = new Page_types();
        $result = $page_types->all();

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_NOT_FOUND,
            "error_message" => lang('not_found_error'),
            'data' => [],
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);

    }

    public function templates_get()
    {
        $this->load->helper('directory');
        $layouts = directory_map('./application/views/site/layouts', 1);
        $templates = directory_map('./application/views/site/templates', 1);

        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => [
                'layouts' => $layouts ? $layouts : [],
                'templates' => $templates ? $templates : [],
            ]
        );
        
        $this->response($response, REST_Controller::HTTP_OK);
        
    }

}
