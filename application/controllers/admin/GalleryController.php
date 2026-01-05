<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class GalleryController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/AlbumModel', 'Album');
    }

    public function index()
    {
        $this->renderAdminView('admin.gallery.albums_list', 'Galería', 'Galería de Imagenes');
    }

    public function items($albumid = '')
    {
        if (!$albumid) {
            $this->index();
            return;
        }

        $album = $this->findOrFail(new AlbumModel(), $albumid, 'Album no encontrado :(');
        $this->renderAdminView('admin.gallery.albums_items', 'Galería', 'Galería de Imagenes');
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.gallery.new_form', 'Nuevo Album', 'Agregar nuevo Album', [
            'album_id' => null,
            'editMode' => null
        ]);
    }

    public function editar($album_id)
    {
        $this->renderAdminView('admin.gallery.new_form', 'Editar Album', 'Editar Album', [
            'album_id' => $album_id,
            'editMode' => 'edit'
        ]);
    }

}
