<?php

require APPPATH . 'libraries/REST_Controller.php';
use ReallySimpleJWT\Token;

class Login extends REST_Controller
{

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('LoginMod');

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
    public function index_post()
    {
        $this->lang->load('login_lang', 'english');
        $this->load->helper('language');

        if ($this->input->post('username') && $this->input->post('password')) {
            $password = $this->input->post('password');
            $username = $this->input->post('username');
            $isLoged = $this->LoginMod->isLoged($username, $password);
            if ($isLoged) {
                $userId = $isLoged[0]->id;
                $secret = 'sec!ReT423*&';
                $expiration = time() + 3600;
                $issuer = 'localhost';
                $token = Token::create($userId, $secret, $expiration, $issuer);
                $this->response(['token' => $token, 'data' => $isLoged[0]], REST_Controller::HTTP_OK);
            } else {
                $data = array('error_message' => lang('username_or_password_invalid'), 'error_code' => 2);
                $this->response($data, REST_Controller::HTTP_UNAUTHORIZED);

            }
        } else {
            $data = array('error_message' => lang('username_or_password_not_found'), 'error_code' => 3);
            $this->response($data, REST_Controller::HTTP_UNAUTHORIZED);
        }

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('items', $input, array('id' => $id));

        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_delete($id)
    {
        $this->db->delete('items', array('id' => $id));

        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);
    }

}
