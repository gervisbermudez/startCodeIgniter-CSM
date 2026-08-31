<?php

require APPPATH . 'libraries/REST_Controller.php';

class VideosController extends REST_Controller
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
        $this->load->model('Admin/VideoModel');
    }

    public function index_get($video_id = null)
    {
        if (!$this->require_video_permision('SELECT_VIDEOS')) {
            return;
        }

        $video = new VideoModel();
        if ($video_id) {
            $found = $video->find($video_id);
            $result = $found ? $video : [];
            if ($result) {
                $this->response_ok($result);
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
        $this->respond_index_list($video, $where, array(), $options);
    }

    public function index_post()
    {
        $id = $this->input->post('id');
        $is_update = ($id !== null && $id !== '' && $id !== false);
        if (!$this->require_video_permision($is_update ? 'UPDATE_VIDEO' : 'CREATE_VIDEO')) {
            return;
        }

        $nombre = $this->input->post('nombre');
        if (!$nombre) {
            $this->response_error('Nombre is required', [], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $status = $this->input->post('status');
        if ($status === null || $status === '' || $status === false) {
            $status = 2;
        }
        $status = (int) $status;
        if ($status !== 1 && $status !== 2) {
            $status = 2;
        }

        $paypal = $this->input->post('paypal');
        $payinfo = ($paypal === null) ? '' : $paypal;

        $data = [
            'nombre' => $nombre,
            'description' => $this->input->post('description'),
            'duration' => $this->input->post('duration'),
            'youtubeid' => $this->input->post('youtubeid'),
            'preview' => $this->input->post('preview'),
            'payinfo' => $payinfo,
            'fecha' => date('Y-m-d H:i:s'),
            'status' => $status,
        ];

        $video = new VideoModel();
        if ($is_update) {
            if (!$video->find($id)) {
                $this->response_error(lang('not_found_error'));
                return;
            }
            $ok = $video->update_video(['id' => $id], $data);
            if ($ok) {
                $video->find($id);
                system_logger('videos', $video->video_id, "updated", "A video has been updated");
                $this->response_ok($video);
                return;
            }
        } else {
            $inserted = $video->set_video($data);
            if ($inserted) {
                $video->find($inserted);
                system_logger('videos', $video->video_id, "created", "A video has been created");
                $this->response_ok($video);
                return;
            }
        }

        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_delete($video_id = null)
    {
        if (!$this->require_video_permision('DELETE_VIDEO')) {
            return;
        }

        if (!$video_id) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $video = new VideoModel();
        if ($video->find($video_id)) {
            if ($video->delete()) {
                system_logger('videos', $video->video_id, ("deleted"), ("A video has been deleted"));
                $this->response_ok($video);
                return;
            } else {
                $this->response_error(lang('unexpected_error'));
                return;
            }
        }

        $this->response_error(lang('not_found_error'));
    }

    /**
     * @param mixed $permision
     * @return bool
     */
    protected function require_video_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }
}
