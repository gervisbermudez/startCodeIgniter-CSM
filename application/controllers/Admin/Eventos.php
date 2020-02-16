<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Eventos extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('EventosMod');
    }

    public function index()
    {
        $data['base_url'] = $this->config->base_url();
        $data['username'] = $this->session->userdata('username');
        $data['title'] = "Admin | Eventos";
        $data['h1'] = "Eventos";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['arrayEventos'] = $this->EventosMod->get_event('all');

        echo $this->blade->view("admin.eventos.eventos_list", $data);

    }

    public function Ver($id = '')
    {
        $array = $this->EventosMod->get_event(array('id' => $id), '1', '');
        if ($array) {
            $data['modalid'] = random_string('alnum', 16);
            $this->load->library('menu');
            $this->menu->char = "'";
            $this->menu->set_ul_properties($properties = array('class' => 'dropdown-content', 'role' => 'menu', 'id' => 'options'));
            $links = array('Ver en la web' => array('target' => '_blank', 'href' => base_url('index.php/Eventos/ver/' . $id)));
            if (5 > $this->session->userdata('level')) {
                $links['Editar'] = array('href' => base_url('index.php/Admin/Eventos/Editar/' . $id));
            }
            if (3 > $this->session->userdata('level')) {
                $links['Eliminar'] = array(
                    'class' => 'modal-trigger',
                    'href' => "#" . $data['modalid'],
                    'data-action-param' => '{"id":"' . $id . '", "table":"eventos"}',
                    'data-url' => "admin/eventos/fn_ajax_delete_data/",
                    'data-url-redirect' => "admin/eventos/",
                    'data-ajax-action' => "inactive",
                );
            }
            $data['options'] = $this->menu->get_menu($links);
            $data['evento'] = $array[0];
            $data['title'] = "Admin | Evento";
            $data['h1'] = "Detalles de un evento";
            $data['header'] = $this->load->view('admin/header', $data, true);
            // Load the views
            echo $this->blade->view("admin.eventos.evento", $data);

        } else {
            $this->showError();
        }
    }

    public function Agregar()
    {
        $data['base_url'] = $this->config->base_url();
        $data['title'] = "Admin | Nuevo Evento";
        $data['h1'] = "Agregar nuevo Evento";
        $data['header'] = $this->load->view('admin/header', $data, true);
        $data['footer_includes'] = array('tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
            'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['advlist autolink lists link image charmap print preview anchor','searchreplace visualblocks code fullscreen','insertdatetime media table contextmenu imagetools'] });</script>");
        echo $this->blade->view("admin.eventos.crear_evento", $data);

    }

    public function save()
    {
        // set the url base
        $data['base_url'] = $this->config->base_url();
        if ($this->input->post('nombre')) {
            if (!$this->EventosMod->setEventData()) {
                $event = $this->EventosMod->get_event(array('nombre' => $this->input->post('nombre')), '', '');
                $id = $event[0]['id'];
                $this->load->model('ModRelations');
                $relations = array('id_user' => $this->session->userdata('id'), 'tablename' => 'eventos', 'id_row' => $id, 'action' => 'crear');
                $this->ModRelations->set_relation($relations);
                redirect('admin/eventos/ver/' . $id);
            } else {
                $this->showError();
            }
        }
    }

    public function Editar($id = "")
    {

        if ($id != "") {
            $this->load->model('EventosMod');
            $array = $this->EventosMod->get_event(array('id' => $id), '');
            if ($array) {
                $data['eventdata'] = $array[0];
                $data['title'] = "Admin | Editar Evento";
                $data['h1'] = "Editar un evento";
                $data['header'] = $this->load->view('admin/header', $data, true);
                $data['footer_includes'] = array('tinymce' => '<script src="' . base_url('public/js/tinymce/js/tinymce/tinymce.min.js') . '"></script>',
                    'tinymceinit' => "<script>tinymce.init({ selector:'textarea',  plugins : ['advlist autolink lists link image charmap print preview anchor','searchreplace visualblocks code fullscreen','insertdatetime media table contextmenu imagetools'] });</script>");
                // Load the views
                echo $this->blade->view("admin.eventos.editar_evento", $data);

            } else {
                $this->showError();
            }
        } else {
            $this->index();
        }
    }

    public function update($id = "")
    {
        if ($id != "") {
            $array = $this->EventosMod->get_event(array('id' => $id), '');
            if ($array) {
                if ($this->EventosMod->updateEvent($id)) {
                    $this->Ver($id);
                } else {
                    $this->showError();
                }
            } else {
                $this->showError();
            }
        } else {
            $this->index();
        }
    }
}
