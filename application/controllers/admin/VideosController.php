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
        $all = $this->Video->all();
        $videos = [];
        if ($all) {
            // Normalize each item into the array shape expected by the blade templates
            foreach ($all as $item) {
                $videos[] = $this->normalizeVideo($item);
            }
        }

        $this->renderAdminView('admin.videos.videos_listado', 'Videos', 'Videos', [
            'videos' => $videos
        ]);
    }

    /**
     * Normalize a video model/row into the legacy array shape used by views
     * @param mixed $item object or array
     * @return array
     */
    protected function normalizeVideo($item)
    {
        // convert to object for easier property access
        $obj = is_array($item) ? (object) $item : $item;

        $video_id = isset($obj->video_id) ? $obj->video_id : (isset($obj->id) ? $obj->id : null);
        $nam = isset($obj->nam) ? $obj->nam : (isset($obj->nombre) ? $obj->nombre : '');
        $preview = isset($obj->preview) ? $obj->preview : (isset($obj->imagen) ? $obj->imagen : '');
        $status = isset($obj->status) ? (string) $obj->status : '0';
        $fecha = isset($obj->date_publish) ? $obj->date_publish : (isset($obj->fecha) ? $obj->fecha : (isset($obj->date_create) ? $obj->date_create : ''));
        $youtube = isset($obj->youtube_id) ? $obj->youtube_id : (isset($obj->youtubeid) ? $obj->youtubeid : '');

        return [
            'id' => $video_id,
            'video_id' => $video_id,
            'nombre' => $nam,
            'nam' => $nam,
            'preview' => $preview,
            'status' => $status,
            'fecha' => $fecha,
            'date_publish' => $fecha,
            'description' => isset($obj->description) ? $obj->description : '',
            'duration' => isset($obj->duration) ? $obj->duration : '',
            'payinfo' => isset($obj->payinfo) ? $obj->payinfo : '',
            'youtubeid' => $youtube,
            'youtube_id' => $youtube,
        ];
    }

    public function categorias($categoria = 'all')
    {
        $this->renderAdminView('admin.videos.videos_listado', 'Videos', 'Categorias de Videos', [
            'categorias' => $this->Video->get_categoria(['tipo' => 'video'])
        ]);
    }

    public function ver($id = 'all')
    {
        if ($id == 'all') {
            $this->index();
        } else {

            $this->Video->find($id);
            $data['video'] = $this->normalizeVideo($this->Video);

            if ($data['video']) {

                $data['title'] = ADMIN_TITLE . " | Videos";
                $data['h1'] = $data['video']['nombre'];
                $data['header'] = $this->load->view('admin/header', $data, true);
                $data['categorias'] = $this->Video->get_video_categoria_rel(array('`video-categoria`.`id_video`' => $id));
                $data['modalid'] = random_string('alnum', 16);
                $data['itemid'] = random_string('alnum', 16);
                $this->load->library('menu');
                $this->menu->char = "'";
                $this->menu->set_ul_properties($properties = array('class' => 'dropdown-content', 'role' => 'menu', 'id' => 'options'));
                $links = array('Ver en la web' => array('target' => '_blank', 'href' => base_url('videos/ver/' . $id)));
                if (5 > $this->session->userdata('level')) {
                    $links['Editar'] = array('href' => base_url('admin/videos/editar/' . $id));
                }
                if (3 > $this->session->userdata('level')) {
                    $links['Eliminar'] = array('href' => '#' . $data['modalid'],
                        'class' => 'modal-trigger',
                        'data-action-param' => '{"id":"' . $id . '", "table":"video"}',
                        'data-url' => 'admin/videos/fn_ajax_delete_data',
                        'data-url-redirect' => 'admin/videos/',
                        'data-ajax-action' => 'inactive',
                    );
                }
                $data['options'] = $this->menu->get_menu($links);
                echo $this->blade->view("admin.videos.ver", $data);
            } else {
                $this->showError('El video al que intenta acceder no existe :(');
            }
        }
    }

    public function nuevo()
    {
        $this->load->helper('array');

        $this->renderAdminView('admin.videos.crear', 'Nuevo', 'Videos', [
            'video' => [],
            'action' => 'admin/videos/save',
            'videocategoria' => [],
            'categorias' => $this->Video->get_categoria(['tipo' => 'video'])
        ]);
    }
    public function save()
    {
        $status = 0;
        if ($this->input->post('status') === '1') {
            $status = 1;
        }
        $fecha = new DateTime;
        $data = array(
            'id' => 'NULL',
            'nombre' => $this->input->post('nombre'),
            'description' => $this->input->post('description'),
            'duration' => $this->input->post('duration'),
            'youtubeid' => $this->input->post('youtubeid'),
            'preview' => $this->input->post('preview'),
            'payinfo' => $this->input->post('paypal'),
            'fecha' => $fecha->format('Y-m-d H:i:s'),
            'status' => $status,
        );
        $inserted_id = $this->Video->set_video($data);
        if ($inserted_id) {
            if ($this->input->post('categorias')) {
                foreach ($this->input->post('categorias') as $key => $value) {
                    $this->Video->set_video_categoria(array('id' => 'NULL', 'id_video' => $inserted_id, 'id_categoria' => $value));
                }
            }
            $this->load->model('ModRelations');
            $relations = array('user_id' => $this->session->userdata('id'), 'tablename' => 'video', 'id_row' => $inserted_id, 'action' => 'crear');
            $this->ModRelations->set_relation($relations);

            if ($this->input->is_ajax_request()) {
                // respond with JSON for AJAX saves
                header('Content-Type: application/json');
                echo json_encode([
                    'code' => 200,
                    'data' => ['id' => $inserted_id, 'redirect' => base_url('admin/videos/ver/' . $inserted_id)],
                ]);
                return;
            }

            redirect('admin/videos/ver/' . $inserted_id);
        } else {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(['code' => 500, 'error' => 'save_failed']);
                return;
            }
            $this->showError();
        }
    }

    public function editar($id)
    {

        $this->Video->find($id);
        $data['video'] = $this->normalizeVideo($this->Video);
        if ($data['video']) {
            $this->load->helper('array');

            $data['title'] = ADMIN_TITLE . " | Videos";
            $data['h1'] = "Videos";
            $data['header'] = $this->load->view('admin/header', $data, true);
            $data['action'] = 'admin/videos/update';
            // already loaded above
            $data['video'] = $this->normalizeVideo($this->Video);
            $data['categorias'] = $this->StModel->get_data(array('tipo' => 'Video'), 'categorias');

            $videocategoria = $this->Video->get_video_categoria(array('`id_video`' => $id));
            $data['videocategoria'] = array();
            if ($videocategoria) {
                foreach ($videocategoria as $key => $value) {
                    $data['videocategoria'][$value['id_categoria']] = true;
                }
            }
            $data['footer_includes'] = array();
            echo $this->blade->view("admin.videos.crear", $data);

        } else {
            $this->showError();
        }
    }

    public function update()
    {
        $status = 0;
        if ($this->input->post('status') === '1') {
            $status = 1;
        }
        $where = array('id' => $this->input->post('id'));
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'description' => $this->input->post('description'),
            'duration' => $this->input->post('duration'),
            'youtubeid' => $this->input->post('youtubeid'),
            'preview' => $this->input->post('preview'),
            'payinfo' => $this->input->post('paypal'),
            'status' => $status,
        );
        if ($this->Video->update_video($where, $data)) {
            $this->Video->delete_video_categoria(array('id_video' => $this->input->post('id')));
            foreach ($this->input->post('categorias') as $key => $value) {
                $this->Video->set_video_categoria(array('id' => 'NULL', 'id_video' => $this->input->post('id'), 'id_categoria' => $value));
            }
            redirect('admin/videos/ver/' . $this->input->post('id'));
        } else {
            $this->showError();
        }
    }

}
