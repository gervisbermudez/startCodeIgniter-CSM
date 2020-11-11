<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Categorias extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Categories');
    }

    public function index()
    {
        $data['h1'] = "Todas las Categorias";
        $data['title'] = ADMIN_TITLE . " | Categorias";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.categorias.categorias_list", $data);

    }

    public function nueva()
    {
        $data['title'] = ADMIN_TITLE . " | Categorias";
        $data['h1'] = "Nueva Categoria";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['categorie_id'] = '';
        $data['editMode'] = 'new';

        echo $this->blade->view("admin.categorias.new_form", $data);
    }

    public function editar($categorie_id)
    {
        $data['title'] = ADMIN_TITLE . " | Categorias";
        $data['h1'] = "Editar Categoria";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['categorie_id'] = $categorie_id;
        $data['editMode'] = 'edit';

        echo $this->blade->view("admin.categorias.new_form", $data);
    }

    public function ajax_get_categorie_type()
    {
        $this->output->enable_profiler(false);
        $categorie_type = $this->input->post('categorie_type');

        if ($categorie_type) {
            $categories = new Categories();
            $categories = $categories->where(array('type' => $categorie_type, 'parent_id' => '0'));

            $response = array(
                'code' => 200,
                'data' => $categories ? $categories : [],
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            $error_message = 'Tipo no encontrada';
            $response = array(
                'code' => 404,
                'error_message' => $error_message,
                'data' => $_POST,
            );
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

    }

    public function ajax_get_subcategorie_type()
    {
        $this->output->enable_profiler(false);
        $parent_id = $this->input->post('parent_id');

        if ($parent_id) {
            $categories = new Categories();
            $categories = $categories->where(array('parent_id' => $parent_id));

            $response = array(
                'code' => 200,
                'data' => $categories ? $categories : [],
            );

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            $error_message = 'Tipo no encontrada';
            $response = array(
                'code' => 404,
                'error_message' => $error_message,
                'data' => $_POST,
            );
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

    }

}
