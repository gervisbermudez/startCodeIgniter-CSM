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
        $this->renderAdminView('admin.galeria.albumes_list', 'Galería', 'Galería de Imagenes');
    }

    public function items($albumid = '')
    {
        if (!$albumid) {
            $this->index();
            return;
        }

        $album = $this->findOrFail(new Album(), $albumid, 'Album no encontrado :(');
        $this->renderAdminView('admin.galeria.albumes_items', 'Galería', 'Galería de Imagenes');
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.galeria.new_form', 'Nuevo Album', 'Agregar nuevo Album', [
            'album_id' => null,
            'editMode' => null
        ]);
    }

    public function editar($album_id)
    {
        $this->renderAdminView('admin.galeria.new_form', 'Editar Album', 'Editar Album', [
            'album_id' => $album_id,
            'editMode' => 'edit'
        ]);
    }

}
