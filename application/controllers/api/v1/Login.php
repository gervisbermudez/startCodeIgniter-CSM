<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require APPPATH . 'libraries/REST_Controller.php';

class Login extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Admin/LoginMod');
    }

    /**
     * @api {post} /login/ Auth the client into the Start CMS API
     * @apiName login
     * @apiGroup Login
     *
     * @apiParam {string} username The username of the user.
     * @apiParam {string} password The password of the user.
     *
     *
     * @apiSuccess {integer} status The status code of the request.
     * @apiSuccess {string} token  The JWT token.
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoiMTgifQ.NftK7Sr9Iez248IvAaSg4qmZRYVA9IlDoWOSS-sARWQ"
     * }
     *
     * @apiError UserOrPasswordNotFound The <code>username</code> or <code>password</code> was not found.
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 401 Unauthorized
     * {
     *   "error_message": "Username or password not found",
     *   "error_code": 3
     * }
     * @apiError Unauthorized Invalid <code>username</code> or <code>password</code>
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 401 Unauthorized
     * {
     *   "error_message": "Invalid username or password",
     *   "error_code": 2
     * }
     */
    public function index_post()
    {
        $this->lang->load('login_lang', 'english');

        if ($this->input->post('username') && $this->input->post('username')) {
            $password = $this->input->post('password');
            $username = $this->input->post('username');
            $login_data = $this->LoginMod->isLoged($username, $password);
            if ($login_data) {
                $this->session->set_userdata('logged_in', true);
                foreach ($login_data[0] as $key => $value) {
                    if ($key != 'user_data') {
                        $this->session->set_userdata($key, $value);
                    } else {
                        foreach ($value as $index => $val) {
                            $this->session->set_userdata($index, $val);
                        }
                    }
                }
                $this->load->model('Admin/User');
                $user = new User();
                $user->find_with(["username" => $username]);
                $user->lastseen = date("Y-m-d H:i:s");
                $user->save();
                $this->load->model('Admin/Usergroup');
                $usergroup = new Usergroup();
                $result = $usergroup->find_with(array("usergroup_id" => $user->usergroup_id));
                $this->session->set_userdata("usergroup_permisions", $usergroup->usergroup_permisions);

                $rand_key = random_string('alnum', 16);
                // Check if valid user
                // Create a token from the user data and send it as reponse
                $token = AUTHORIZATION::generateToken(['userdata' => $this->session->all_userdata(), 'rand_key' => $rand_key]);
                $this->session->set_userdata('token', $token);
                // Prepare the response
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'userdata' => $login_data, 'token' => $token, 'auth' => 'valid', 'redirect' => 'admin'];
                $this->response($response, $status);
                //$this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->session->sess_destroy();
                $data = array('error_message' => lang('username_or_password_invalid'), 'error_code' => 2);
                $this->response($data, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->session->sess_destroy();
            $data = array('error_message' => lang('username_or_password_not_found'), 'error_code' => 3);
            $this->response($data, REST_Controller::HTTP_UNAUTHORIZED);
        }

    }

    /**
     * Delete session
     *
     * @return Response
     */
    public function logout_get()
    {
        $this->session->sess_destroy();
        $this->response_ok(true);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_get($id = 0)
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_put()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_delete()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);

    }

}
