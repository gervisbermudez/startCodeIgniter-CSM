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
        $video = new VideoModel();
        if ($video_id) {
            $found = $video->find($video_id);
            $result = $found ? $video : [];
        } else {
            // Use the model helper to return mapped results (includes relations)
            $result = $video->all();
            $result = $result ? (is_array($result) ? $result : $result->toArray()) : [];
        }

        if ($result || (is_array($result) && count($result) === 0)) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

    public function index_post()
    {
        // Minimal validation
        $nombre = $this->input->post('nombre');
        if (!$nombre) {
            $this->response_error('Nombre is required', [], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $data = [
            'nombre' => $nombre,
            'description' => $this->input->post('description'),
            'duration' => $this->input->post('duration'),
            'youtubeid' => $this->input->post('youtubeid'),
            'preview' => $this->input->post('preview'),
            'payinfo' => $this->input->post('paypal'),
            'fecha' => date('Y-m-d H:i:s'),
            'status' => $this->input->post('status') ? $this->input->post('status') : 0,
        ];

        $video = new VideoModel();
        $inserted = $video->set_video($data);
        if ($inserted) {
            // fetch the created video and return the mapped model (PagesController pattern)
            $video->find($inserted);
            system_logger('videos', $video->video_id, ($this->input->post('video_id') ? "updated" : "created"), ($this->input->post('video_id') ? "A video has been updated" : "A video has been created"));
            $this->response_ok($video);
            return;
        }

        $this->response_error(lang('unexpected_error'), [], REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_delete($video_id = null)
    {
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
}
