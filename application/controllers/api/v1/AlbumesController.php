<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class AlbumesController extends REST_Controller
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
        $this->load->model('Admin/AlbumModel');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($album_id = null)
    {
        if (!$this->require_gallery_permision('SELECT_GALLERY')) {
            return;
        }

        $album = new AlbumModel();
        if ($album_id) {
            $result = $album->where(array("album_id" => $album_id));
            if ($result) {
                $this->response_ok($result->first());
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $status = $this->get('status');
        $where = array();
        $options = array('unfiltered' => true);
        if ($status !== null && $status !== '') {
            $where['status'] = $status;
            $options = array();
        }
        $this->respond_index_list($album, $where, array(), $options);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        $album_id = $this->input->post('album_id');
        $is_update = ($album_id !== null && $album_id !== '' && $album_id !== false);
        if (!$this->require_gallery_permision($is_update ? 'UPDATE_GALLERY' : 'CREATE_GALLERY')) {
            return;
        }

        $this->load->library('FormValidator');
        $form = new FormValidator();

        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[5]'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'required|min_length[5]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer'),
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

        $album = new AlbumModel();
        if ($is_update) {
            if (!$album->find($album_id)) {
                $this->response_error(lang('not_found_error'));
                return;
            }
            $album->date_update = date("Y-m-d H:i:s");
        } else {
            $album->user_id = userdata('user_id');
            $album->date_create = date("Y-m-d H:i:s");
            $album->date_update = date("Y-m-d H:i:s");
        }
        $album->name = $this->input->post('name');
        $album->description = $this->input->post('description');
        $album->status = $this->input->post('status');
        $album->date_publish = $this->input->post('date_publish') ? $this->input->post('date_publish') : date("Y-m-d H:i:s");

        if ($album->save()) {

            $album_items = $this->input->post("album_items");
            $this->load->model('Admin/AlbumItemsModel');
            if ($album_items) {
                foreach ($album_items as $value) {
                    $item = new AlbumItemsModel();
                    $value['album_item_id'] ? $item->find($value['album_item_id']) : false;
                    $item->album_id = $album->album_id;
                    $item->file_id = $value['file_id'];
                    $item->name = $value['name'];
                    $item->description = $value['description'];
                    $item->status = $value['status'];
                    $item->date_create = date("Y-m-d H:i:s");
                    $item->save();
                }
            }

            system_logger('albumes', $album->album_id, ($is_update ? 'updated' : 'created'), ($is_update ? 'An album has been updated' : 'An album has been created'));
            $this->response_ok($album);
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
    public function index_delete($id = null)
    {
        if (!$this->require_gallery_permision('DELETE_GALLERY')) {
            return;
        }

        if ($id) {
            $album = new AlbumModel();
            if ($album->find($id)) {
                $deleted = $album->delete();
                system_logger('albumes', $album->album_id, 'deleted', 'An album has been deleted');
                $this->response_ok(array("result" => $deleted));
                return;
            }
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
    public function delete_album_item_get($item_album_id)
    {
        $this->delete_album_item($item_album_id);
    }

    /**
     * REST delete for an album item.
     *
     * @param mixed $item_album_id
     * @return void
     */
    public function delete_album_item_delete($item_album_id = null)
    {
        $this->delete_album_item($item_album_id);
    }

    /**
     * @param mixed $item_album_id
     * @return void
     */
    protected function delete_album_item($item_album_id)
    {
        if (!function_exists('has_permisions') || (!has_permisions('UPDATE_GALLERY') && !has_permisions('DELETE_GALLERY'))) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $this->load->model('Admin/AlbumItemsModel');
        $album_items = new AlbumItemsModel();
        if ($item_album_id && $album_items->find($item_album_id)) {
            if ($album_items->delete()) {
                $this->response_ok($album_items);
                return;
            }
        }
        $this->response_error(lang('not_found_error'));
    }

    /**
     * @param mixed $permision
     * @return bool
     */
    protected function require_gallery_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }

}
