<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class Categorie extends REST_Controller
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
        $this->load->model('Admin/Categories');

    }

    /**
     * Este método es llamado cuando se hace una petición GET a la URL del endpoint de categorías
     * Recibe un parámetro opcional "categorie_id" que indica el ID de la categoría a buscar
     * @api {get} /api/v1/categorie/:categorie_id Request Categorie information
     * @apiName GetCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} categorie_id Categorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "categorie_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "categorie_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:28",
     *               "status": "1"
     *           },
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */

    public function index_get($categorie_id = null)
    {
        // Se crea una instancia de la clase Categories para acceder a los métodos de la tabla categories de la BD
        $categorie = new Categories();
        // Si se recibe un parámetro "categorie_id", se busca la categoría correspondiente en la BD
        if ($categorie_id) {
            // Se hace una consulta a la BD para buscar la categoría con "parent_id" = 0 y "categorie_id" = $categorie_id
            $result = $categorie->where(array('parent_id' => '0', 'categorie_id' => $categorie_id));
            // Si se encuentra una categoría con ese ID, se guarda en $result. De lo contrario, se guarda un arreglo vacío
            $result = $result ? $result->first() : [];
        } else {
            // Si no se recibe un parámetro "categorie_id", se buscan todas las categorías cuyo "parent_id" = 0
            $result = $categorie->where(array('parent_id' => '0'));
        }

        // Si se encontró una categoría, se responde con un código de éxito (200) y se envía la categoría como respuesta
        if ($result) {
            $this->response_ok($result);
            return;
        }

        // Si no se encontró una categoría, se responde con un error de "no encontrado" (404)
        $this->response_error(lang('not_found_error'));
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function index_post()
    {
        // Cargamos la biblioteca 'FormValidator'
        $this->load->library('FormValidator');
        // Creamos una nueva instancia de 'FormValidator'
        $form = new FormValidator();

        // Definimos la configuración de validación para los campos de entrada
        $config = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'required|min_length[1]'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'required|min_length[1]'),
            array('field' => 'type', 'label' => 'type', 'rules' => 'required|min_length[1]'),
            array('field' => 'parent_id', 'label' => 'parent_id', 'rules' => 'integer|is_natural'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'required|integer|is_natural'),
        );

        // Configuramos las reglas de validación con la configuración almacenada en la variable $config
        $form->set_rules($config);

        // Verificamos si la validación ha fallado
        if (!$form->run()) {
            // Si la validación ha fallado, respondemos con un error que incluye una cadena de errores y una respuesta HTTP 400 (solicitud incorrecta)
            $this->response_error(lang('validations_error'), ['errors' => $form->_error_array], REST_Controller::HTTP_BAD_REQUEST, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Si la validación ha sido exitosa, creamos una nueva instancia de la clase 'Categories' y asignamos los valores de entrada a sus propiedades
        $categorie = new Categories();

        $this->input->post('categorie_id') ? $categorie->find($this->input->post('categorie_id')) : false;
        $categorie->name = $this->input->post('name');
        $categorie->description = $this->input->post('description');
        $categorie->type = $this->input->post('type');
        $categorie->user_id = userdata('user_id');
        $categorie->type = $this->input->post('type');
        $categorie->status = $this->input->post('status');
        $categorie->date_create = date("Y-m-d H:i:s");
        $categorie->date_publish = date("Y-m-d H:i:s");
        $categorie->parent_id = $this->input->post('parent_id');

        // Guardamos los datos en la base de datos. Si tiene éxito, la respuesta HTTP 200 (éxito) se devuelve junto con los datos guardados en forma de objeto 'Categories'.
        if ($categorie->save()) {
            $this->response_ok($categorie);
            return;
        }

        // Si falla la operación de guardar, respondemos con un error que indica que ocurrió un error inesperado y devuelve una respuesta HTTP 400 (solicitud incorrecta).
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
    public function index_delete($categorie_id = null)
    {
        $categorie = new Categories();
        $result = $categorie->find($categorie_id);
        if ($result) {
            $result = $categorie->delete($categorie_id);
        }

        if ($result) {
            $this->response_ok($result);
            return;
        }

        $this->response_error(lang('not_found_error'));
    }

    /**
     * Este método es llamado cuando se hace una petición GET a la URL del endpoint de subcategorías
     * Recibe dos parámetros: "categorie_id" indica el ID de la categoría padre y "subcategorie_id" indica el ID de la subcategoría a buscar (opcional)
     * @api {get} /api/v1/categorie/subcategorie/:categorie_id/:subcategorie_id Request SubCategorie information
     * @apiName GetSubCategorie
     * @apiGroup Categorie
     *
     * @apiParam {Number} categorie_id Categorie unique ID.
     * @apiParam {Number} subcategorie_id SubCategorie unique ID.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "categorie_id": "4",
     *               "name": "SubCategoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "categorie_id": "5",
     *               "name": "SubCategoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:28",
     *               "status": "1"
     *           },
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    /**
     * Obtiene una subcategoría en base a su ID y la ID de su categoría padre.
     * Si no se especifica una subcategoría ID, devuelve todas las subcategorías de la categoría padre especificada.
     *
     * @param int $categorie_id La ID de la categoría padre.
     * @param int|null $subcategorie_id La ID de la subcategoría. Opcional.
     */
    public function subcategorie_get($categorie_id, $subcategorie_id = null)
    {
        $categorie = new Categories();

        /** Si se especificó una subcategoría ID, obtiene la subcategoría con esa ID y la ID de su categoría padre. */
        if ($subcategorie_id) {
            $result = $categorie->where(array('parent_id' => $categorie_id, 'categorie_id' => $subcategorie_id));
            $result = $result ? $result->first() : [];
        } else {
            /** De lo contrario, obtiene todas las subcategorías de la categoría padre especificada. */
            $result = $categorie->where(array('parent_id' => $categorie_id));
        }

        /** Si se encontró la subcategoría o subcategorías, devuelve la respuesta con código 200. */
        if ($result) {
            $this->response_ok($result);
            return;
        }

        /** De lo contrario, devuelve un error de "no encontrado". */
        $this->response_error(lang('not_found_error'));
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function subcategorie_post()
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function subcategorie_put($id)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);

    }

    /**
     * Get All Data from this method.
     *
     * @return Response
     */
    public function subcategorie_delete($id = null)
    {
        $data = array();
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    /**
     * Obtiene todas las categorías principales de un determinado tipo.
     * @api {get} /api/v1/categorie/type/:type/ Request Categorie information
     * @apiName GetCategorieType
     * @apiGroup Categorie
     *
     * @apiParam {String} type Categorie Categorie type name.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "categorie_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "categorie_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:28",
     *               "status": "1"
     *           },
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function type_get($type = 0)
    {
        $categorie = new Categories();
        $result = $categorie->where(array('parent_id' => '0', 'type' => $type));

        if ($result) {
            /** Si se encontraron resultados, devuelve un código de estado "ok". */
            $this->response_ok($result);
            return;
        }

        /** Si no se encontraron resultados, devuelve un código de estado "not found". */
        $this->response_error(lang('not_found_error'));
    }

    /**
     * Obtiene todas las categorías que coincidan con los parámetros de filtrado enviados a través de la petición GET.
     * @api {get} /api/v1/categorie/type/:type/ Request Categorie information
     * @apiName GetCategorieType
     * @apiGroup Categorie
     *
     * @apiParam {String} type Categorie Categorie type name.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "code": 200,
     *       "data": [
     *           {
     *               "categorie_id": "4",
     *               "name": "Categoria 1",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:20",
     *               "status": "1"
     *           },
     *           {
     *               "categorie_id": "5",
     *               "name": "Categoria 2",
     *               "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim numquam dignissimos repudiandae iure adipisci tempora vel dolorum perspiciatis excepturi non earum nisi soluta quibusdam voluptatibus, cum minima nam? Incidunt, dolor!",
     *               "type": "page",
     *               "parent_id": "0",
     *               "date_publish": "2020-04-19 10:36:10",
     *               "date_create": "2020-04-19 10:36:14",
     *               "date_update": "2020-04-19 10:40:28",
     *               "status": "1"
     *           },
     *       ]
     *   }
     *
     * @apiError CategorieNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "error_message": "Resource not found",
     *     "data": []
     * }
     */
    public function filter_get()
    {
        /* Crea una instancia de Categories */
        $categorie = new Categories();

        /** Realiza la búsqueda de categorías utilizando los parámetros de filtrado de la petición GET. */
        $result = $categorie->where($_GET);

        if ($result) {
            /** Si se encontraron resultados, devuelve un código de estado "ok". */
            $this->response_ok($result);
            return;
        }

        /** Si no se encontraron resultados, devuelve un código de estado "not found". */
        $this->response_error(lang('not_found_error'));
    }

}
