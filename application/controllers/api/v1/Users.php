<?php

require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller
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

        if (!$this->session->userdata('logged_in')) {
            $this->lang->load('login_lang', 'english');
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
                'error_message' => lang('user_not_authenticated'),
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }

        $this->load->database();
        $this->load->model('Admin/User');
        $this->lang->load('users_lang', 'english');

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($user_id = null)
    {
        $result = $this->User->get_full_info($user_id);
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
            array('field' => 'username', 'label' => 'username', 'rules' => 'required|min_length[5]|max_length[18]|alpha_numeric'),
            array('field' => 'password', 'label' => 'password', 'rules' => 'required|min_length[5]|max_length[18]|regex_match[/^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[#?!@$%^&*\-_]).{8,}$/]'),
            array('field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email'),
            array('field' => 'usergroup_id', 'label' => 'usergroup_id', 'rules' => 'required|integer|is_natural_no_zero'),
        );

        $form->set_rules($config);

        if (!$form->run()) {
            $response = array(
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'error_message' => lang('new_user_validations_error'),
                'errors' => $form->_error_array,
                'request_data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $usuario = new User();
        $this->input->post('user_id') ? $usuario->find($this->input->post('user_id')) : false;
        $usuario->username = $this->input->post('username');
        $usuario->password = $this->input->post('password');
        $usuario->email = $this->input->post('email');
        $usuario->lastseen = date("Y-m-d H:i:s");
        $usuario->usergroup_id = $this->input->post('usergroup_id');
        $usuario->status = 1;
        $usuario->user_data = $this->input->post('user_data');

        if ($usuario->save()) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $usuario,
            );

            $this->response($response, REST_Controller::HTTP_OK);
            return;

        } else {

            $response = array(
                'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR,
                'error_message' => lang('new_user_unexpected_error'),
                'data' => $_POST,
            );

            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
        $this->output->enable_profiler(false);
        $usuario = new User();
        $usuario->find($id);
        if ($usuario->delete()) {
            $response = array(
                'code' =>  REST_Controller::HTTP_OK,
                'data' => $usuario,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        } else {
            $response = array(
                'code' => REST_Controller::HTTP_NOT_FOUND,
                'error_message' => lang('user_not_found_error'),
                'data' => $_POST,
            );
            $this->response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function usergroups_get()
    {
        $this->load->model('Admin/Usergroup');

        $result = $this->Usergroup->where(array('level >' => userdata('level')));

        if ($result) {
            $response = array(
                'code' => REST_Controller::HTTP_OK,
                'data' => $result,
            );
            $this->response($response, REST_Controller::HTTP_OK);
            return;
        }

        $response = array(
            'code' => REST_Controller::HTTP_NOT_FOUND,
            'data' => $result,
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);
    }

    public function usergroups_post()
    {
        $this->response([], REST_Controller::HTTP_OK);
    }

    public function avatar_post()
    {
        $user_id = $this->input->post('user_id');
        $avatar = $this->input->post('avatar');

        $usuario = new User();
        $result = false;

        if ($usuario->find($user_id)) {

            if(isset($usuario->user_data->avatar)){
                $usuario->user_data->avatar = $avatar;
                $result = $usuario->save();
            }else{
                $insert = array(
                    'user_id' => $user_id,
                    '_key' => 'avatar',
                    '_value' => $avatar,
                    'status' => 1,
                );
                $result = $usuario->set_user_data($insert);
            }

            if($result){
                $response = array(
                    'code' => REST_Controller::HTTP_OK,
                    'data' => $result,
                );
                $this->response($response, REST_Controller::HTTP_OK);
                return;
            }
        }

        $response = array(
            'code' => REST_Controller::HTTP_BAD_REQUEST,
            'data' => $result,
            'user' => $usuario
        );
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
    }
}
