<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Controller extends CI_Controller
{
    // Un array que contiene los permisos requeridos para cada ruta
    public $routes_permisions = [];

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Blade');
        load_site_config_map();

        // Carga el archivo de idioma para el panel de administración en inglés
        $this->lang->load('admin/admin', 'english');

        // Si el usuario no ha iniciado sesión, redirige a la página de inicio de sesión
        // Excepto si ya está en la página de login
        if (!$this->session->userdata('logged_in') && get_class($this) !== 'LoginController') {
            $uri = urlencode(uri_string());
            redirect('admin/login/?redirect=' . $uri);
        }

        if ($this->session->userdata('logged_in')) {
            $this->refreshUsergroupPermisions();
        }

        // Si el perfilador está habilitado, lo activa para la salida
        if ($this->config->item('enable_profiler')) {
            $this->output->enable_profiler(true);
        }
    }

    /**
     * Reload group permissions when the session predates a new permision row.
     */
    private function refreshUsergroupPermisions()
    {
        $usergroup_id = $this->session->userdata('usergroup_id');
        if (!$usergroup_id) {
            return;
        }
        $cache_key = 'usergroup_permisions_' . (int) $usergroup_id;
        $perms = get_cached($cache_key);
        if (!is_array($perms)) {
            $this->load->model('Admin/UsergroupModel');
            $usergroup = new UsergroupModel();
            $usergroup->usergroup_id = $usergroup_id;
            $perms = $usergroup->usergroup_permisions();
            if (!is_array($perms)) {
                $perms = array();
            }
            set_cached($cache_key, $perms, 3600);
        }
        $this->session->set_userdata('usergroup_permisions', $perms);
    }

    // Verifica los permisos requeridos para la ruta actual
    public function check_permisions()
    {
        $uri_string = uri_string();
        $matches = [];

        // Itera a través de las rutas y los permisos requeridos
        foreach ($this->routes_permisions as $method => $route) {
            $patern = $route["patern"];
            $permissions = $route["required_permissions"];

            // Compara la ruta actual con el patrón
            if (preg_match($patern, $uri_string, $matches)) {
                // Verifica los permisos requeridos para la ruta actual
                foreach ($permissions as $key => $value) {
                    if (!has_permisions($value)) {
                        // Si el usuario no tiene los permisos requeridos, muestra un error y detiene la ejecución
                        $this->showError(lang('not_have_permissions'));
                        die();
                    }
                }
            }
        }
    }

    // Muestra un mensaje de error en una página en blanco
    public function showError($errorMsg = 'Ocurrio un error inesperado', $data = array('title' => 'Error', 'h1' => 'Error'))
    {
        $data['conten'] = $errorMsg;

        // Carga la vista del encabezado
        $data['header'] = $this->load->view('admin/header', $data, true);

        // Carga la vista en blanco y muestra el mensaje de error
        echo $this->blade->view("admin.blank_page", $data);
    }

    /**
     * Prepara los datos comunes para las vistas del panel de administración
     * Reduce código duplicado en los controladores
     * 
     * @param string $title Título de la página (se agregará ADMIN_TITLE como prefijo)
     * @param string $h1 Encabezado H1 de la página (opcional)
     * @param array $additionalData Datos adicionales a incluir
     * @return array Array con los datos preparados
     */
    protected function prepareAdminData($title, $h1 = '', $additionalData = [])
    {
        $data = [
            'title' => ADMIN_TITLE . ' | ' . $title,
            'h1' => $h1,
            'username' => $this->session->userdata('username'),
            'base_url' => $this->config->base_url(),
        ];

        return array_merge($data, $additionalData);
    }

    /**
     * Renderiza una vista del panel de administración con datos preparados
     * Incluye automáticamente el header
     * 
     * @param string $view Nombre de la vista (notación con puntos)
     * @param string $title Título de la página
     * @param string $h1 Encabezado H1
     * @param array $additionalData Datos adicionales
     * @return void
     */
    protected function renderAdminView($view, $title, $h1 = '', $additionalData = [])
    {
        $data = $this->prepareAdminData($title, $h1, $additionalData);
        $data['header'] = $this->load->view('admin/header', $data, true);

        echo $this->blade->view($view, $data);
    }

    /**
     * Busca un modelo por ID o lanza un error si no existe
     * Elimina código duplicado de validación de existencia de recursos
     * 
     * @param object $model Instancia del modelo a buscar
     * @param mixed $id ID del recurso a buscar
     * @param string $errorMessage Mensaje de error personalizado
     * @return object Instancia del modelo encontrado
     */
    protected function findOrFail($model, $id, $errorMessage = 'Recurso no encontrado')
    {
        if (!$model->find($id)) {
            $this->showError($errorMessage);
            exit();
        }
        return $model;
    }

    /**
     * Obtiene datos de sesión del usuario actual
     * Acceso simplificado a datos comunes de sesión
     * 
     * @param string $key Clave específica de sesión (opcional)
     * @return mixed Valor de sesión o array con datos del usuario
     */
    protected function getUserSession($key = null)
    {
        if ($key !== null) {
            return $this->session->userdata($key);
        }

        return [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'email' => $this->session->userdata('email'),
            'usergroup_id' => $this->session->userdata('usergroup_id'),
        ];
    }

    // Muestra la página de error 404
    public function error404()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "404";
        $url = uri_string();

        // Si la URL no contiene 'admin', muestra la página de error 404 normal
        if (stristr($url, 'admin') === false) {
            echo $this->blade->view("error404", $data);
        } else {
            // Si el usuario no ha iniciado sesión, redirige a la página de inicio de sesión
            // Check if the user is logged in, if not redirect to login page
            if (!$this->session->userdata('logged_in')) {
                // Get the current URI
                $uri = urlencode(uri_string());
                // Redirect to the login page with the current URI as a parameter
                redirect('admin/login/?redirect=' . $uri);
            } else {
                // If the user is logged in, display a 404 error page
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.blank_page", $data);
            }
        }

    }

}

/**
 * Base_Controller - Controlador base para los controladores de la aplicación
 * Hereda de CI_Controller de CodeIgniter y agrega funcionalidades comunes
 */
class Base_Controller extends CI_Controller
{
    /**
     * Variable pública para el controlador de temas
     * @var ThemeController
     */
    public $themeController = null;

    /**
     * Constructor de la clase
     * Carga la configuración, el lenguaje y la biblioteca Track_Visitor
     * Crea una instancia del controlador de temas local
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('Blade');

        // Carga la configuración
        $this->load_config();

        // Carga el lenguaje del sitio
        $this->lang->load('site', 'english');

        // Carga la biblioteca Track_Visitor mejorada y rastrea los visitantes si la configuración lo permite
        $this->load->library('Track_Visitor_Enhanced', null, 'track_visitor');
        if (config('SITEM_TRACK_VISITORS') == 'Si') {
            $this->track_visitor->visitor_track();
        }

        // Carga el controlador de temas local
        if (!class_exists('ThemeController_Base', false)) {
            require_once APPPATH . 'libraries/ThemeController_Base.php';
        }
        $themeControllerPath = getThemePath() . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'ThemeController.php';
        if (!file_exists($themeControllerPath)) {

            // Si el controlador de temas no existe en el tema actual, carga el controlador de temas predeterminado
            $themeControllerPath = getThemePath("awesomeTheme") . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'ThemeController.php';
        }
        include $themeControllerPath;
        $this->themeController = new ThemeController();
    }

/**
 * Devuelve un array de metadatos para una página específica. Si no hay metadatos disponibles para la página, se establecen algunos metadatos predeterminados.
 *
 * @param object $page - un objeto que contiene información de la página actual.
 * @return array - un array de metadatos.
 */
    public function getPageMetas($page)
    {
        // Verifica si hay metadatos disponibles en la página
        if (isset($page->page_data["meta"])) {
            // Define una función anónima que convierte un array en un objeto.
            function convertArrayToObject($value)
            {
                // Convierte el valor en un array.
                $value = (Array) $value;

                // Si la propiedad 'property' es 'og:image', establece la imagen predeterminada si no hay contenido.
                if (isset($value['property']) && $value['property'] == 'og:image') {
                    if ($value['content'] != '') {
                        $value['content'] = base_url($value['content']);
                    } else {
                        $value['content'] = base_url('public/img/default.jpg');
                    }
                }
                // Si la propiedad 'property' es 'twitter:image', establece la imagen predeterminada si no hay contenido.
                if (isset($value['property']) && $value['property'] == 'twitter:image') {
                    if ($value['content'] != '') {
                        $value['content'] = base_url($value['content']);
                    } else {
                        $value['content'] = base_url('public/img/default.jpg');
                    }
                }
                // Retorna el valor convertido en objeto.
                return $value;
            }
            // Aplica la función 'convertArrayToObject' a cada elemento del array 'meta'.
            return array_map("convertArrayToObject", $page->page_data["meta"]);

        }

        // Establece algunos metadatos predeterminados.
        $description = config('SITE_DESCRIPTION');
        $url = base_url(uri_string());
        $title = config('SITE_TITLE');

        // Si existe el contenido de la página, utiliza ese contenido como descripción, título y URL.
        if (isset($page->content)) {
            $description = strip_tags($page->content);
            $title = $page->title;
            $url = base_url($page->path);
        }

        // Si existe una imagen principal, utiliza la URL de esa imagen. De lo contrario, utiliza la imagen predeterminada.
        if (isset($page->main_image)) {
            $imagen_url = base_url($page->main_image->file_front_path);
        } else {
            $imagen_url = base_url(getThemePublicPath() . 'images/default-brand.jpg');
        }

        // Retorna un array con los metadatos predeterminados y cualquier metadato adicional encontrado en la página.
        $default_metas = array(
            array('name' => 'keywords', 'content' => $title),
            array('name' => 'description', 'content' => character_limiter($description, 120)),
            array('name' => 'ROBOTS', 'content' => 'NOODP'),
            array('name' => 'GOOGLEBOT', 'content' => 'INDEX, FOLLOW'),
            array('property' => 'og:title', 'content' => $title),
            array('property' => 'og:description', 'content' => character_limiter($description, 120)),
            array('property' => 'og:site_name', 'content' => config('SITE_TITLE')),
            array('property' => 'og:url', 'content' => $url),
            array('property' => 'og:image', 'content' => $imagen_url),
            array('property' => 'og:type', 'content' => $title),
            array('name' => 'twitter:card', 'content' => 'summary_large_image'),
            array('name' => 'twitter:site', 'content' => '@gervisbermudez'),
            array('name' => 'twitter:creator', 'content' => '@gervisbermudez'),
            array('name' => 'twitter:title', 'content' => $title),
            array('name' => 'twitter:description', 'content' => character_limiter($description, 120)),
            array('name' => 'twitter:image', 'content' => $imagen_url),
        );
        return $default_metas;
    }

    // Definición del método para manejar errores 404
    public function error404()
    {
        // Configuración del header para indicar el error 404
        $this->output->set_status_header(404);
        // Se obtiene la URL base de la aplicación
        $data['base_url'] = $this->config->base_url();

        // Se establece el título de la página
        $data['title'] = "Page not found | 404";

        // Se obtiene la URI actual
        $url = uri_string();

        // Si la URI no contiene la cadena 'admin', se trata de una URL pública
        if (stristr($url, 'admin') === false) {

            // Se obtiene la ruta del tema actual, si existe
            if (getThemePath()) {
                $this->blade->changePath(getThemePath());
            }

            // Se obtiene el ID de la página de error 404 desde la configuración
            $page_id = config("SITE_ERROR_404_PAGE_ID");

            // Si se obtuvo el ID de la página, se obtiene la información de la página
            if ($page_id) {
                $data = $this->get_page_info(array('page_id' => $page_id, 'status' => 1));
            }

            // Si se obtuvo la información de la página de error 404, se carga la vista correspondiente del tema actual
            if ($page_id && $data !== null) {
                $data['base_url'] = $this->config->base_url();
                $data['title'] = "Page not found | 404";
                echo $this->themeController->error404($data);
            }

            // Si no se obtuvo la información de la página de error 404, se carga la vista predeterminada de error 404
            else {
                $data['base_url'] = $this->config->base_url();
                $data['title'] = "Page not found | 404";
                echo $this->blade->view("site.error404", $data);
            }
        }

        // Si la URI contiene la cadena 'admin', se trata de una URL de administrador
        else {

            // Si el usuario no ha iniciado sesión, se redirecciona a la página de inicio de sesión con la URI actual como parámetro
            if (!$this->session->userdata('logged_in')) {
                $uri = urlencode(uri_string());
                redirect('admin/login/?redirect=' . $uri);
            }

            // Si el usuario ha iniciado sesión, se carga la vista de página en blanco para el área de administración
            else {
                $data['h1'] = "404 Page not found :(";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.blank_page", $data);
            }
        }

    }

    // Esta función recibe como parámetro una condición para buscar información de la página
    public function get_page_info($where)
    {
        // Se crea una instancia de la clase PageModel para buscar la información de la página
        $pageInfo = new PageModel();
        // Se guarda en un array la información obtenida de la página
        $data['result'] = $pageInfo->find_with($where);
        // Si la página no existe, se devuelve null
        if (!$data['result']) {
            //Página no encontrada
            return null;
        }
        $data['pagePublishTime'] = !empty($pageInfo->date_publish)
            ? DateTime::createFromFormat('Y-m-d H:i:s', $pageInfo->date_publish)
            : false;

        $canPreview = (bool) $this->session->userdata('logged_in');
        if (!$canPreview && !page_is_live_for_public($pageInfo)) {
            return null;
        }

        // Se guarda la información de la página en el array $data
        $data['page'] = $pageInfo;
        $data['meta'] = $this->getPageMetas($pageInfo);
        // Se obtiene el título de la página
        $data['title'] = isset($pageInfo->page_data["title"]) ? config("SITE_TITLE") . " - " . $pageInfo->page_data["title"] : config("SITE_TITLE") . " - " . $pageInfo->title;
        // Se obtiene el layout de la página
        $data['layout'] = $pageInfo->layout == 'default' ? 'site' : $pageInfo->layout;
        // Se obtienen los headers includes de la página
        $data['headers_includes'] = isset($pageInfo->page_data["headers_includes"]) ? $pageInfo->page_data["headers_includes"] : "";
        // Se obtienen los footer includes de la página
        $data['footer_includes'] = isset($pageInfo->page_data["footer_includes"]) ? $pageInfo->page_data["footer_includes"] : "";
        // Se obtiene el template de la página
        $data['template'] = $pageInfo->template == 'default' ? 'templates.default' : $pageInfo->template;

        if (!empty($data['page']->content)) {
            $this->load->helper('general');
            $page_id = isset($data['page']->page_id) ? (int) $data['page']->page_id : 0;
            $content_key = $page_id > 0 ? ('page_content_' . $page_id) : '';
            $cached_content = $content_key !== '' ? get_cached($content_key) : null;
            if (is_string($cached_content)) {
                $data['page']->content = $cached_content;
            } else {
                $expanded = expand_page_embeds($data['page']->content);
                $expanded = expand_content_helpers($expanded);
                $data['page']->content = $expanded;
                if ($content_key !== '') {
                    set_cached($content_key, $expanded, 86400);
                }
            }
        }

        // Se devuelve el array con la información de la página
        return $data;

    }

    /** Carga una configuración específica
     */
    private function load_config()
    {
        load_site_config_map();
        $this->load_theme_overrides();
    }

    /**
     * Theme PHP config/routes. Small files; not cached so theme edits apply immediately.
     *
     * @return void
     */
    private function load_theme_overrides()
    {
        $theme_path = getThemePath();
        if (!$theme_path) {
            return;
        }

        $config = array();
        $route = array();
        $configPath = $theme_path . '/config/';
        $this->load->helper('directory');
        $map = directory_map($configPath);
        if ($map) {
            foreach ($map as $file) {
                if (is_string($file) && strpos($file, '.php') !== false) {
                    include $configPath . $file;
                }
            }
        }
        if (!empty($config) && is_array($config)) {
            foreach ($config as $config_name => $config_value) {
                $this->config->set_item($config_name, $config_value);
            }
        }
        if (!empty($route) && is_array($route)) {
            $this->router->routes = array_merge($this->router->routes, $route);
        }
    }

    /**
     * Serve anonymous HTML from cache, or build and store it.
     * $builder returns HTML string or null (404).
     *
     * @param string $cache_key
     * @param callable $builder
     * @param int $alias_page_id Refresh page_html_id_* on cache hit when known
     * @return bool false when builder returned null
     */
    protected function echo_public_html($cache_key, $builder, $alias_page_id = 0)
    {
        $alias_page_id = (int) $alias_page_id;
        if (can_cache_public_html()) {
            $html = get_cached($cache_key);
            if (is_string($html) && $html !== '') {
                if ($alias_page_id > 0) {
                    remember_page_html_alias($alias_page_id, $cache_key);
                }
                echo $html;
                return true;
            }
        }
        $html = call_user_func($builder);
        if ($html === null) {
            return false;
        }
        if (can_cache_public_html() && is_string($html) && $html !== '') {
            set_cached($cache_key, $html, 3600);
        }
        echo $html;
        return true;
    }

}

/* End of file MY_Controller */
