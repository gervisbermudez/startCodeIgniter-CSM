<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Galeria extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Album');
    }

    public function index()
    {
        $data['title'] = ADMIN_TITLE . " | Galería";
        $data['h1'] = "Galería de Imagenes";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.galeria.albumes_list", $data);
    }

    public function items($albumid = '')
    {
        if (!$albumid) {
            $this->index();
        } else {
            $album = new Album();
            $album = $album->find($albumid);
            if ($album) {
                $data['title'] = ADMIN_TITLE . " | Galería";
                $data['h1'] = "Galería de Imagenes";
                $data['header'] = $this->load->view('admin/header', $data, true);
                echo $this->blade->view("admin.galeria.albumes_items", $data);

            } else {
                $this->showError('Album no encontrado :(');
            }
        }
    }

    public function crearalbum()
    {
        $data['title'] = ADMIN_TITLE . " | Nuevo Album";
        $data['h1'] = "Agregar nuevo Album";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['footer_includes'] = array(
            'tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
            'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['link table'] });</script>");

        echo $this->blade->view("admin.galeria.crearalbum", $data);

    }

}
