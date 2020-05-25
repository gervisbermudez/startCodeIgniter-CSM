<?php

require APPPATH . 'libraries/REST_Controller.php';

class Forms extends REST_Controller
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
        $this->load->model('Admin/Form_custom');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($form_id = null)
    {
        $form = new Form_custom();
        if ($form_id) {
            $result = $form->find($form_id);
            $result = $result ? $form : [];
        } else {
            $result = $form->all();
        }

        if ($result) {
            $response = array(
                'code' => 200,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        if($form_id){
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
        }else{
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
            array('field' => 'path', 'label' => 'path', 'rules' => 'required|min_length[5]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'required|min_length[5]'),
            array('field' => 'page_type_id', 'label' => 'page_type_id', 'rules' => 'integer|is_natural_no_zero'),
            array('field' => 'categorie_id', 'label' => 'categorie_id', 'rules' => 'integer|is_natural'),
            array('field' => 'subcategorie_id', 'label' => 'subcategorie_id', 'rules' => 'integer|is_natural'),
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

        $form = new Page();
        $this->input->post('page_id') ? $form->find($this->input->post('page_id')) : false;
        $form->title = $this->input->post('title');
        $form->subtitle = $this->input->post('subtitle');
        $form->path = $this->input->post('path');
        $form->content = $this->input->post('content');
        $form->user_id = userdata('user_id');
        $form->page_type_id = $this->input->post('page_type_id');
        $form->status = $this->input->post('status');
        $form->template = $this->input->post('template');
        $form->layout = $this->input->post('layout');
        $form->date_publish = $this->input->post('publishondate') == 'true' ? date("Y-m-d H:i:s") : $this->input->post('date_publish');
        $form->date_create = date("Y-m-d H:i:s");
        $form->visibility = $this->input->post('visibility');
        $form->categorie_id = $this->input->post('categorie_id');
        $form->subcategorie_id = $this->input->post('subcategorie_id');
        $form->mainImage = $this->input->post('mainImage');
        $form->page_data = array(
            "twitter:card" => "summary",
            "twitter:title" => "How to write a different PHP?"
        );
        if ($form->save()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $form,
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
        $form = new Page();
        $form->find($id);
        if ($form->delete()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $form,
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
        $this->load->model('Admin/Page_type');

        $form_type = new Page_type();
        $result = $form_type->all();

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

    public function editpageinfo_get($form_id)
    {
        $this->load->model('Admin/Page_type');
        $this->load->helper('directory');

        $form = new Page();
        $result = false;

        if ($form_id) {
            $result = $form->find($form_id);
        }

        if(!$result){
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        //Types
        $form_type = new Page_type();
        $form_types = $form_type->all();

        if(!$form_types){
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                "error_message" => lang('not_found_error'),
                'data' => [],
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        //Templates

        $layouts = directory_map('./application/views/site/layouts', 1);
        $templates = directory_map('./application/views/site/templates', 1);        
        
        $response = array(
            'code' => 200,
            'data' => array(
                'page'          => $form,
                'page_types'    => $form_types,
                'layouts' => $layouts ? $layouts : [],
                'templates' => $templates ? $templates : [],
            ),
        );
        
        $this->response($response, REST_Controller::HTTP_OK);
        return;
    }

}
