<?php

require APPPATH . 'libraries/REST_Controller.php';

class PagesController extends REST_Controller
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
        $this->load->model('Admin/PageModel');

    }

    public function index_get($page_id = null)
    {
        if (!$this->require_page_permision('SELECT_PAGES')) {
            return;
        }

        $page = new PageModel();
        if ($page_id) {
            $result = $page->find($page_id);
            $result = $result ? $page : [];
            if ($result) {
                $this->response_ok($result);
                return;
            }
            $this->pages_error(lang('not_found_error'));
            return;
        }

        $this->respond_index_list($page, $this->pages_list_filters());
    }

    /**
     * Filtros del listado admin: status, filters[] (whitelist), o publicados + borradores.
     *
     * @return array
     */
    protected function pages_list_filters()
    {
        $status = $this->get('status');
        if ($status !== null && $status !== '') {
            return $this->whitelist_page_filters(array('status' => $status));
        }
        $filters = $this->get('filters');
        if ($filters) {
            $whitelisted = $this->whitelist_page_filters(is_array($filters) ? $filters : array());
            if (!empty($whitelisted)) {
                return $whitelisted;
            }
        }
        return array('status_in' => array(1, 2));
    }

    public function index_post()
    {
        $page_id = $this->input->post('page_id');
        $is_update = ($page_id !== null && $page_id !== '' && $page_id !== false);

        if (!$this->require_page_permision($is_update ? 'UPDATE_PAGE' : 'CREATE_PAGE')) {
            return;
        }

        $this->load->library('FormValidator');
        $form = new FormValidator();

        $config = array(
            array('field' => 'title', 'label' => 'title', 'rules' => 'required|min_length[5]'),
            array('field' => 'path', 'label' => 'path', 'rules' => 'required|min_length[1]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'required|min_length[5]'),
            array('field' => 'page_type_id', 'label' => 'page_type_id', 'rules' => 'integer|is_natural_no_zero'),
            array('field' => 'categorie_id', 'label' => 'categorie_id', 'rules' => 'integer|is_natural'),
            array('field' => 'subcategorie_id', 'label' => 'subcategorie_id', 'rules' => 'integer|is_natural'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
            array('field' => 'visibility', 'label' => 'visibility', 'rules' => 'integer|is_natural_no_zero'),
        );

        $form->set_rules($config);

        $errors = array();
        if (!$form->run()) {
            $errors = $form->_error_array;
        }

        $status = (int) $this->input->post('status');
        if (!in_array($status, array(1, 2, 3), true)) {
            $errors['status'] = 'The status field must be 1, 2 or 3';
        }

        $path = trim((string) $this->input->post('path'));
        if (!$this->is_safe_page_path($path)) {
            $errors['path'] = 'The path field must be a safe slug';
        }

        if (!empty($errors)) {
            $this->pages_error(
                lang('validations_error'),
                REST_Controller::HTTP_BAD_REQUEST,
                REST_Controller::HTTP_BAD_REQUEST,
                array('errors' => $errors)
            );
            return;
        }

        $page = new PageModel();
        if ($is_update) {
            if (!$page->find($page_id)) {
                $this->pages_error(lang('not_found_error'));
                return;
            }
        }

        if ($this->page_path_taken($path, $is_update ? $page_id : null)) {
            $this->pages_error(
                lang('validations_error'),
                REST_Controller::HTTP_BAD_REQUEST,
                REST_Controller::HTTP_BAD_REQUEST,
                array('errors' => array('path' => 'The path field must contain a unique value.'))
            );
            return;
        }

        $page->title = $this->input->post('title');
        $page->subtitle = $this->input->post('subtitle');
        $page->path = $path;
        $page->content = $this->input->post('content');
        $page->json_content = $this->input->post('json_content');
        $page->page_type_id = $this->input->post('page_type_id');
        $page->status = $status;
        $page->template = $this->input->post('template');
        $page->layout = $this->input->post('layout');
        $page->date_publish = $this->normalize_date_publish($this->input->post('date_publish'));
        $page->visibility = $this->input->post('visibility');
        $page->categorie_id = $this->optional_fk($this->input->post('categorie_id'));
        $page->subcategorie_id = $this->optional_fk($this->input->post('subcategorie_id'));
        $page->mainImage = $this->input->post('mainImage') ? $this->input->post('mainImage') : null;
        $page->thumbnailImage = $this->input->post('thumbnailImage') ? $this->input->post('thumbnailImage') : null;
        $page->{"page_data"} = $this->input->post('page_data');

        if (!$is_update) {
            $page->user_id = userdata('user_id');
            $page->date_create = date("Y-m-d H:i:s");
        }

        if ($page->save()) {
            invalidate_page_cache($page);
            system_logger('pages', $page->page_id, ($is_update ? "updated" : "created"), ($is_update ? "A page has been updated" : "A page has been created"));
            $this->response_ok($page);
            return;
        }

        $this->pages_error(lang('unexpected_error'), REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);

    }

    public function index_delete($id = null)
    {
        if (!$this->require_page_permision('DELETE_PAGE')) {
            return;
        }

        $page = $this->load_page_or_fail($id);
        if (!$page) {
            return;
        }

        if ($page->delete()) {
            invalidate_page_cache($page);
            system_logger('pages', $page->page_id, ("deleted"), ("A page has been deleted"));
            $this->response_ok($page);
            return;
        }

        $this->pages_error(lang('unexpected_error'));
    }

    public function archive_post($id = null)
    {
        if (!$this->require_page_permision('UPDATE_PAGE')) {
            return;
        }

        $page = $this->load_page_or_fail($id);
        if (!$page) {
            return;
        }

        $page->status = 3;
        if ($page->save()) {
            invalidate_page_cache($page);
            system_logger('pages', $page->page_id, ("archive"), ("A page has been archived"));
            $this->response_ok($page);
            return;
        }

        $this->pages_error(lang('unexpected_error'));
    }

    public function restore_post($id = null)
    {
        if (!$this->require_page_permision('UPDATE_PAGE')) {
            return;
        }

        $page = $this->load_page_or_fail($id);
        if (!$page) {
            return;
        }

        $page->status = 2;
        if ($page->save()) {
            invalidate_page_cache($page);
            system_logger('pages', $page->page_id, ("restore"), ("A page has been restored"));
            $this->response_ok($page);
            return;
        }

        $this->pages_error(lang('unexpected_error'));
    }

    public function types_get()
    {
        if (!$this->require_page_permision('SELECT_PAGES')) {
            return;
        }

        $this->load->model('Admin/PageTypeModel');

        $page_type = new PageTypeModel();
        $result = $page_type->all();

        $this->response_ok($result ? $result : array());
    }

    public function templates_get()
    {
        if (!$this->require_page_permision('SELECT_PAGES')) {
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_OK,
            'data' => getTemplates(),
        );

        $this->response($response, REST_Controller::HTTP_OK);

    }

    public function editpageinfo_get($page_id = false)
    {
        if (!$this->require_page_permision('SELECT_PAGES')) {
            return;
        }

        $this->load->model('Admin/PageTypeModel');
        $this->load->helper('directory');

        $page = new PageModel();
        $result = false;

        if ($page_id) {
            $result = $page->find($page_id);
        }

        if (!$result) {
            $this->pages_error(lang('not_found_error'));
            return;
        }

        $page_type = new PageTypeModel();
        $page_types = $page_type->all();
        if (!$page_types) {
            $page_types = array();
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

    public function autocomplete_get()
    {
        if (!$this->require_page_permision('SELECT_PAGES')) {
            return;
        }

        $search = $this->input->get("search");
        $search = is_string($search) ? $search : '';
        $page = new PageModel();
        $result = $page->find_list(array('status_in' => array(1, 2)), array(), array(), $search);

        $items = array();
        if ($result) {
            $items = array_map(function ($value) {
                return array(
                    "href" => base_url($value->path),
                    "name" => $value->title,
                    "description" => character_limiter(strip_tags($value->content), 120),
                );
            }, $result->toArray());
        }

        $this->response(array(
            "items" => $items,
            "success" => true,
        ), REST_Controller::HTTP_OK);
    }

    public function duplicate_post($id = null)
    {
        if (!$this->require_page_permision('UPDATE_PAGE')) {
            return;
        }

        $page = $this->load_page_or_fail($id);
        if (!$page) {
            return;
        }

        $page->page_id = null;
        $page->title = "Copy of " . $page->title;
        $page->status = 2;
        $page->path = $this->unique_copy_path($page->path);
        $page->date_create = date('Y-m-d H:i:s');
        $page->date_publish = null;
        $page->map = false;

        if ($page->save()) {
            invalidate_page_cache($page);
            system_logger('pages', $page->page_id, ("duplicate"), ("A page has been duplicated"));
            $fresh = new PageModel();
            $fresh->find($page->page_id);
            $this->response_ok($fresh);
            return;
        }

        $this->pages_error(lang('unexpected_error'));
    }

    /**
     * @param mixed $permision
     * @return bool
     */
    protected function require_page_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->pages_error('You do not have permission to perform this action', REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }

    /**
     * Error de pages: { code, error_message, data: [] } sin POST. HTTP 200 salvo validación.
     *
     * @param string $error_message
     * @param int $code
     * @param int $http_status
     * @param array $extra
     * @return void
     */
    protected function pages_error($error_message, $code = REST_Controller::HTTP_NOT_FOUND, $http_status = REST_Controller::HTTP_OK, $extra = array())
    {
        $response = array(
            'code' => $code,
            'data' => array(),
            'error_message' => $error_message,
        );
        if ($extra) {
            $response = array_merge($response, $extra);
        }
        $this->response($response, $http_status);
    }

    /**
     * @param mixed $id
     * @return PageModel|false
     */
    protected function load_page_or_fail($id)
    {
        if ($id === null || $id === '' || $id === false) {
            $this->pages_error(lang('not_found_error'));
            return false;
        }
        $page = new PageModel();
        if (!$page->find($id)) {
            $this->pages_error(lang('not_found_error'));
            return false;
        }
        return $page;
    }

    /**
     * @param array $filters
     * @return array
     */
    protected function whitelist_page_filters($filters)
    {
        $allowed = array('status', 'status_in', 'page_type_id', 'categorie_id', 'user_id');
        $out = array();
        foreach ($allowed as $key) {
            if (array_key_exists($key, $filters)) {
                $out[$key] = $filters[$key];
            }
        }
        return $out;
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function is_safe_page_path($path)
    {
        if (!is_string($path) || $path === '') {
            return false;
        }
        if (strpos($path, '..') !== false) {
            return false;
        }
        return (bool) preg_match('#^[A-Za-z0-9][A-Za-z0-9/_-]*$#', $path);
    }

    /**
     * Unique path among non-deleted rows. Exclude self on update.
     *
     * @param string $path
     * @param mixed $exclude_page_id
     * @return bool
     */
    protected function page_path_taken($path, $exclude_page_id = null)
    {
        $this->db->from('page');
        $this->db->where('path', $path);
        $this->db->where('status !=', 0);
        if ($exclude_page_id !== null && $exclude_page_id !== '' && $exclude_page_id !== false) {
            $this->db->where('page_id !=', $exclude_page_id);
        }
        $this->db->limit(1);
        return $this->db->get()->num_rows() > 0;
    }

    /**
     * @param string $base_path
     * @return string
     */
    protected function unique_copy_path($base_path)
    {
        $path = $base_path . '-copy';
        $n = 2;
        while ($this->page_path_taken($path) && $n < 100) {
            $path = $base_path . '-copy-' . $n;
            $n++;
        }
        return $path;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function optional_fk($value)
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        if ((int) $value === 0) {
            return null;
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function normalize_date_publish($value)
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        return $value;
    }

}
