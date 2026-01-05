<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class VideosController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/VideoModel', 'Video');
    }

    public function index()
    {
        $this->renderAdminView('admin.videos.videos_list', lang('menu_videos'), lang('videos_all'));
    }

    public function nuevo()
    {
        $this->load->helper('array');
        $categorias = [];
        if (method_exists($this->Video, 'get_categoria')) {
            $categorias = $this->Video->get_categoria(['tipo' => 'video']);
        }
        $this->renderAdminView('admin.videos.create', lang('menu_videos'), lang('videos_new'), [
            'video_id' => '',
            'editMode' => 'new',
            'action' => 'admin/videos/save',
            'video' => [],
            'categorias' => $categorias,
            'videocategoria' => []
        ]);
    }

    public function editar($video_id)
    {
        $this->load->helper('array');
        $this->Video->find($video_id);
        $videoRaw = is_object($this->Video) ? (array) $this->Video : [];
        // Mapear claves del modelo/DB a las usadas en el formulario
        $video = [
            'id' => element('video_id', $videoRaw, ''),
            'nombre' => element('nam', $videoRaw, ''),
            'description' => element('description', $videoRaw, ''),
            'duration' => element('duration', $videoRaw, ''),
            'youtubeid' => element('youtube_id', $videoRaw, ''),
            'payinfo' => element('payinfo', $videoRaw, ''),
            'preview' => element('preview', $videoRaw, ''),
            'status' => element('status', $videoRaw, ''),
            'fecha' => element('date_publish', $videoRaw, ''),
        ];
        $categorias = [];
        if (method_exists($this->Video, 'get_categoria')) {
            $categorias = $this->Video->get_categoria(['tipo' => 'video']);
        }
        $videocategoria = [];
        if (method_exists($this->Video, 'get_video_categoria')) {
            $videocategoriaArr = $this->Video->get_video_categoria(['id_video' => $video_id]);
            if ($videocategoriaArr) {
                foreach ($videocategoriaArr as $cat) {
                    $videocategoria[$cat['id_categoria']] = true;
                }
            }
        }
        $this->renderAdminView('admin.videos.create', lang('menu_videos'), lang('videos_edit'), [
            'video_id' => $video_id,
            'editMode' => 'edit',
            'action' => 'admin/videos/update',
            'video' => $video,
            'categorias' => $categorias,
            'videocategoria' => $videocategoria
        ]);
    }

    public function ver($video_id = false)
    {
        if (!$video_id) {
            $this->error404();
            return;
        }
        $this->Video->find($video_id);
        $video = is_object($this->Video) ? (array) $this->Video : [];
        if (!$video || empty($video['id']) && empty($video['video_id'])) {
            $this->error404();
            return;
        }
        $data = [];
        $data['video'] = $video;
        $data['title'] = ADMIN_TITLE . ' | ' . (isset($video['nombre']) ? $video['nombre'] : 'Video');
        $data['itemid'] = random_string('alnum', 16);
        $data['modalid'] = random_string('alnum', 16);
        $data['categorias'] = method_exists($this->Video, 'get_video_categoria_rel') ? $this->Video->get_video_categoria_rel(['`video-categoria`.`id_video`' => $video_id]) : [];
        $this->load->library('menu');
        $this->menu->char = "'";
        $this->menu->set_ul_properties(['class' => 'dropdown-content', 'role' => 'menu', 'id' => 'options']);
        $links = [
            lang('videos_view') => ['target' => '_blank', 'href' => base_url('videos/ver/' . $video_id)],
        ];
        if (5 > $this->session->userdata('level')) {
            $links[lang('edit')] = ['href' => base_url('admin/videos/editar/' . $video_id)];
        }
        if (3 > $this->session->userdata('level')) {
            $links[lang('delete')] = [
                'href' => '#' . $data['modalid'],
                'class' => 'modal-trigger',
                'data-action-param' => '{"id":"' . $video_id . '", "table":"video"}',
                'data-url' => 'admin/videos/fn_ajax_delete_data',
                'data-url-redirect' => 'admin/videos/',
                'data-ajax-action' => 'inactive',
            ];
        }
        $data['options'] = $this->menu->get_menu($links);
        echo $this->blade->view('admin.videos.view', $data);
    }
}
