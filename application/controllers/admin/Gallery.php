<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Gallery extends MY_Controller
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
            return;
        }

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

    public function nuevo()
    {
        $data['title'] = ADMIN_TITLE . " | Nuevo Album";
        $data['h1'] = "Agregar nuevo Album";
        $data['album_id'] = null;
        $data['editMode'] = null;
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.galeria.new_form", $data);
    }

    public function editar($album_id)
    {
        $data['title'] = ADMIN_TITLE . " | Editar Album";
        $data['h1'] = "Editar Album";
        $data['album_id'] = $album_id;
        $data['editMode'] = "edit";
        $data['header'] = $this->load->view('admin/header', $data, true);
        echo $this->blade->view("admin.galeria.new_form", $data);
    }

}
