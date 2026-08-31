<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class GalleryController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/gallery\/?$/',
            "required_permissions" => ["SELECT_GALLERY"],
            "conditions" => [],
        ],
        "items" => [
            "patern" => '/^admin\/gallery\/items\/(\d+)/',
            "required_permissions" => ["SELECT_GALLERY"],
            "conditions" => [],
        ],
        "nuevo" => [
            "patern" => '/^admin\/gallery\/(nuevo|new)\/?$/',
            "required_permissions" => ["CREATE_GALLERY"],
            "conditions" => [],
        ],
        "editar" => [
            "patern" => '/^admin\/gallery\/(editar|edit)\/(\d+)/',
            "required_permissions" => ["UPDATE_GALLERY"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/AlbumModel', 'Album');
    }

    public function index()
    {
        $this->renderAdminView('admin.gallery.albums_list', lang('menu_albums'), lang('albums_all'));
    }

    public function items($albumid = '')
    {
        if (!$albumid) {
            $this->index();
            return;
        }

        $album = $this->findOrFail(new AlbumModel(), $albumid, lang('albums_not_found'));
        $this->renderAdminView('admin.gallery.albums_items', lang('menu_albums'), lang('albums_all'));
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.gallery.new_form', lang('albums_new'), lang('albums_new'), [
            'album_id' => null,
            'editMode' => null
        ]);
    }

    public function editar($album_id)
    {
        $this->renderAdminView('admin.gallery.new_form', lang('albums_edit'), lang('albums_edit'), [
            'album_id' => $album_id,
            'editMode' => 'edit'
        ]);
    }

}
