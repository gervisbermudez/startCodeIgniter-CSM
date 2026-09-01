<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class LoginController extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->load->database();
        $this->load->model('Admin/LoginModModel', 'LoginMod');
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
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxIn0.signature"
     * }
     *
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

        $username = trim((string) $this->input->post('username'));
        $password = (string) $this->input->post('password');
        $unauth = array(
            'error_message' => lang('username_or_password_invalid'),
            'error_code' => 2,
        );

        if ($this->is_login_rate_limited($username)) {
            $this->response($unauth, REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        if ($username === '' || $password === '') {
            $this->hit_login_rate_limit($username);
            $this->response($unauth, REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $login_data = $this->LoginMod->isLoged($username, $password);
        if (!$login_data) {
            $this->hit_login_rate_limit($username);
            $this->response($unauth, REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $this->load->model('Admin/UserModel');
        $user = new UserModel();
        if (!$user->find((int) $login_data[0]['user_id']) || (int) $user->status !== 1) {
            $this->hit_login_rate_limit($username);
            $this->response($unauth, REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        $this->hydrate_admin_session($user);
        $this->session->sess_regenerate(true);
        $user->lastseen = date('Y-m-d H:i:s');
        $user->save();

        $token = AUTHORIZATION::generateToken(array(
            'sub' => (string) $user->user_id,
            'jti' => bin2hex(random_bytes(16)),
        ));
        $this->session->set_userdata('token', $token);
        $this->clear_login_rate_limit($username);
        system_logger('users', $user->user_id, 'login', 'User logged in');

        $status = parent::HTTP_OK;
        $response = array(
            'status' => $status,
            'userdata' => $login_data,
            'token' => $token,
            'auth' => 'valid',
            'redirect' => 'admin',
        );
        $this->response($response, $status);
    }

    /**
     * Delete session
     *
     * @return Response
     */
    public function logout_get()
    {
        $this->session->sess_destroy();
        if (!$this->input->is_ajax_request()) {
            redirect('admin/login');
            return;
        }
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
