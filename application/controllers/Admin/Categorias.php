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
        $data['categorias'] = $this->Categories->all();
        $data['title'] = "Admin | Categorias";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.categorias.categorias_list", $data);

    }
    public function filter($str_tipo = 'all')
    {
        $data['h1'] = "Todas las Categorias";
        if ($str_tipo == 'all') {
            $data['categorias'] = $this->Categories->get_data('all', 'categorias');
        } else {
            $data['categorias'] = $this->Categories->get_data(array('tipo' => $str_tipo), 'categorias');
            $data['h1'] = "Categoria " . $str_tipo;

        }
        if ($data['categorias']) {
            $data['title'] = "Admin | Categorias";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $this->load->view('admin/head', $data);
            $this->load->view('admin/navbar', $data);
            $this->load->view('admin/categorias/categorias_list', $data);
            $this->load->view('admin/footer', $data);
        } else {
            $this->showError('Tipo de categoria no encontrada :(');
        }
    }
    public function nueva()
    {
        $this->load->helper('array');

        $data['title'] = "Admin | Categorias";
        $data['h1'] = "Nueva Categoria";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['action'] = base_url('admin/categorias/save/');
        $data['categoria'] = array();

        $data['footer_includes'] = array(
            'tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
            'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table'] });</script>");
        /* $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/categorias/new_form', $data);
        $this->load->view('admin/footer', $data); */
        echo $this->blade->view("admin.categorias.new_form", $data);

    }

    public function save()
    {
        $data['title'] = "Admin | Categorias";
        $data['h1'] = "Categorias";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['categorias'] = $this->ModCategorias->get_categoria('all');
        $status = 0;
        if ($this->input->post('status_form') === '1') {
            $status = 1;
        }
        $fecha = new DateTime;

        $data = array('id' => 'null',
            'nombre' => $this->input->post('nombre_form'),
            'description' => $this->input->post('descripcion_form'),
            'tipo' => $this->input->post('tipo_form'),
            'fecha' => $fecha->format('Y-m-d H:i:s'),
            'status' => $status);

        if ($this->ModCategorias->set_categoria($data)) {
            redirect('admin/categorias/');
        } else {
            $this->showError();
        }
    }

    public function update()
    {
        $status = 0;
        if ($this->input->post('status_form') === '1') {
            $status = 1;
        }

        $where = array('id' => $this->input->post('id_form'));
        $data = array('nombre' => $this->input->post('nombre_form'),
            'description' => $this->input->post('descripcion_form'),
            'tipo' => $this->input->post('tipo_form'),
            'status' => $status);

        if ($this->ModCategorias->update_categoria($where, $data)) {
            redirect('admin/categorias/');
        } else {
            $this->showError();
        }
    }

    public function editar($int_id)
    {
        $this->load->helper('array');

        $data['title'] = "Admin | Categorias";
        $data['h1'] = "Editar Categoria";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['action'] = base_url('admin/categorias/update/');
        $data['categoria'] = $this->Categories->get_data(array('id' => $int_id), 'categorias')[0];

        $data['footer_includes'] = array(
            'tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
            'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table'] });</script>");

        $this->load->view('admin/head', $data);
        $this->load->view('admin/navbar', $data);
        $this->load->view('admin/categorias/new_form', $data);
        $this->load->view('admin/footer', $data);
    }

}
