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
        if ($this->input->post('username') && $this->input->post('password')) {
            $password = $this->input->post('password');
            $username = $this->input->post('username');
            $data = array('username' => $username, 'password' => $password, 'user.status' => 1);
            $this->db->select('user.*, usergroup.name, usergroup.level');
            $this->db->join('usergroup', 'user.usergroup = usergroup.id');
            $query = $this->db->get_where('user', $data);

            if ($query->num_rows() > 0) {
                $query = $query->result();

                $userId = $query[0]->id;
                $secret = 'sec!ReT423*&';
                $expiration = time() + 3600;
                $issuer = 'localhost';

                $token = Token::create($userId, $secret, $expiration, $issuer);

                $this->response(['token' => $token], REST_Controller::HTTP_OK);
            } else {
                $this->response(array('Usuario / Password incorrecta'), REST_Controller::HTTP_NOT_FOUND);

            }
        } else {
            $this->response(array('Ingrese Usuario / Contraseña'), REST_Controller::HTTP_NOT_FOUND);
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
