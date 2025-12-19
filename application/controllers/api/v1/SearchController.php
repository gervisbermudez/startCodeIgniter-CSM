<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class SearchController extends REST_Controller
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
     *               "menu_id": "5",
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
    public function index_get()
    {
        $str_term = $this->input->get('q');
        if ($str_term) {
            $this->load->model('Admin/PageModel');
            $pages = new PageModel();
            $data['pages'] = $pages->search($str_term);

            $this->load->model('Admin/UserModel');
            $user = new UserModel();
            $data['users'] = $user->search($str_term);

            $this->load->model('Admin/FileModel');
            $file = new FileModel();
            $data['files'] = $file->search($str_term);

            $this->load->model('Admin/CustomModelModel');
            $custom_model = new CustomModelModel();
            $data['form_customs'] = $custom_model->search($str_term);

            $this->load->model('Admin/CustomModelContentModel');
            $form_content = new CustomModelContentModel();
            $data['form_contents'] = $form_content->search($str_term);

            $this->load->model('Admin/SiteFormModel');
            $siteforms = new SiteFormModel();
            $data['siteforms'] = $siteforms->search($str_term);

            $this->load->model('Admin/SiteFormSubmitModel');
            $siteFormSubmit = new SiteFormSubmitModel();
            $data['siteform_submits'] = $siteFormSubmit->search($str_term);

            $this->load->model('Admin/MenuModel');
            $menu = new MenuModel();
            $data['menus'] = $menu->search($str_term);

            $this->load->model('Admin/CategorieModel');
            $categorie = new CategorieModel();
            $data['categories'] = $categorie->search($str_term);

            $this->load->model('Admin/AlbumModel');
            $album = new AlbumModel();
            $data['albumes'] = $album->search($str_term);

        } else {
            $data = [];
        }
        $this->response_ok($data);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
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
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

}
