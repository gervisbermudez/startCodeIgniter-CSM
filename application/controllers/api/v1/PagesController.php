<?php

require APPPATH . 'libraries/REST_Controller.php';

class PagesController extends REST_Controller
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
        $this->load->helper('general');
        $this->load->model('Admin/PageModel');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($page_id = null)
    {
        $page = new PageModel();
        if ($page_id) {
            $result = $page->find($page_id);
            $result = $result ? $page : [];
        } else if ($this->input->get('filters')) {
            $result = $page->where($this->input->get('filters'));
            $result = $result ? $result->toArray() : [];
        } else {
            $result = $page->where(['status' => '1']);
            $archived = $page->where(['status' => '2']);
            $result = $result ? $result->toArray() : [];
            $archived = $archived ? $archived->toArray() : [];
            $result = array_merge($result, $archived);
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        if ($page_id) {
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

        $page = new PageModel();
        $this->input->post('page_id') ? $page->find($this->input->post('page_id')) : false;
        $page->title = $this->input->post('title');
        $page->subtitle = $this->input->post('subtitle');
        $page->path = $this->input->post('path');
        $page->content = $this->input->post('content');
        $page->json_content = $this->input->post('json_content');
        $page->user_id = userdata('user_id');
        $page->page_type_id = $this->input->post('page_type_id');
        $page->status = $this->input->post('status');
        $page->template = $this->input->post('template');
        $page->layout = $this->input->post('layout');
        $page->date_publish = $this->input->post('publishondate') == 'true' ? date("Y-m-d H:i:s") : $this->input->post('date_publish');
        $page->date_create = date("Y-m-d H:i:s");
        $page->visibility = $this->input->post('visibility');
        $page->categorie_id = $this->input->post('categorie_id');
        $page->subcategorie_id = $this->input->post('subcategorie_id');
        $page->mainImage = $this->input->post('mainImage') ? $this->input->post('mainImage') : null;
        $page->thumbnailImage = $this->input->post('thumbnailImage') ? $this->input->post('thumbnailImage') : null;
        $page->{"page_data"} = $this->input->post('page_data');

        if ($page->save()) {
            system_logger('pages', $page->page_id, ($this->input->post('page_id') ? "updated" : "created"), ($this->input->post('page_id') ? "A page has been updated" : "A page has been created"));
            $this->response_ok($page);

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
        $page = new PageModel();
        $page->find($id);
        if ($page->delete()) {
            system_logger('pages', $page->page_id, ("deleted"), ("A page has been deleted"));
            $this->response_ok($page);
        } else {
            $this->response_error(lang('not_found_error'));
        }
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function archive_post($id = null)
    {
        $page = new PageModel();
        $page->find($id);
        if ($page) {
            $page->status = 3;
            $page->save();
            system_logger('pages', $page->page_id, ("archive"), ("A page has been archive"));
            $this->response_ok($page);
        } else {
            $this->response_error(lang('not_found_error'));
        }
    }

    public function types_get()
    {
        $this->load->model('Admin/PageTypeModel');

        $page_type = new PageTypeModel();
        $result = $page_type->all();

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));

    }

    public function templates_get()
    {
        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => getTemplates(),
        );

        $this->response($response, REST_Controller::HTTP_OK);

    }

    public function editpageinfo_get($page_id = false)
    {
        $this->load->model('Admin/PageTypeModel');
        $this->load->helper('directory');

        $page = new PageModel();
        $result = false;

        if ($page_id) {
            $result = $page->find($page_id);
        }

        if (!$result) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        //Types
        $page_type = new PageTypeModel();
        $page_types = $page_type->all();

        if (!$page_types) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $themeTemplates = getTemplates();

        $response = array(
            'code' => 200,
            'data' => array(
                'page' => $page,
                'page_types' => $page_types,
                'layouts' => $themeTemplates['layouts'],
                'templates' => $themeTemplates['templates'],
            ),
        );

        $this->response($response, REST_Controller::HTTP_OK);
        return;
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function autocomplete_get()
    {
        $search = $this->input->get("search");
        $page = new PageModel();
        $result = $page->search($search);

        if ($result) {

            $response = [
                "items" => array_map(function ($value) {
                    return [
                        "href" => base_url($value->path),
                        "name" => $value->title,
                        "description" => character_limiter(strip_tags($value->content), 120),
                    ];
                }, $result->toArray()),
                "success" => true,
            ];

            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => [],
        );

        $this->response($response, REST_Controller::HTTP_OK);
    }

}