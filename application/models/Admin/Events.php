<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Events extends MY_model
{

    public $primaryKey = 'event_id';
    public $softDelete = true;


    public function __construct()
    {
        parent::__construct();
    }

    public function get_events()
    {
        $sql = "SELECT * FROM `events` WHERE `publishdate`>CURDATE() AND `events`.`status`='publicado' ORDER BY `events`.`publishdate` DESC LIMIT 0, 6";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
	}
	
    public function get_eventsDefeated()
    {
        $sql = "SELECT * FROM `events` WHERE `publishdate`<CURDATE() AND `events`.`status`='publicado' ORDER BY `events`.`publishdate` DESC LIMIT 0, 6";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
	}
	
    public function get_all_events()
    {
        $query = $this->db->get('events');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
	}
	
    public function get_event($data = 'all', $limit = '', $order = array('id', 'DESC'))
    {
        $limit ? $this->db->limit($limit) : null;
        if ($order !== '') {
            $this->db->order_by($order[0], $order[1]);
        }
        if ($data === 'all') {
            $query = $this->db->get('events');
            if ($query->num_rows() > 0) {
                return $query->result_array();
            }
        } else {
            $query = $this->db->get_where('events', $data);
            if ($query->num_rows() > 0) {
                return $query->result_array();
            }
        }
        return false;
	}
	
    public function get_publish_event($id = '')
    {
        if ($id) {
            $query = $this->db->get_where('events', array('id' => $id, 'status' => 'Publicado'));
            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }
        return false;
	}
	
    public function get_eventgallery($event_id = '')
    {
        if (!$event_id) {
            return false;
        }
        $query = $this->db->get_where('album', array('evento' => $event_id));
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
	}
	
    public function deleteEvent($where)
    {
        if (!$where) {
            return false;
        }
        $this->db->where($where);
        return $this->db->delete('events');
	}
	
    public function updateEvent($id = '')
    {
        if (!$id) {
            return false;
        }

        if (!$this->input->post('nombre')) {
            return false;
        }
        $status = "0";
        if ($this->input->post('status') == "on") {
            $status = "1";
        }
        $fecha = DateTime::createFromFormat('j F, Y', $this->input->post('publishdate'));
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'titulo' => $this->input->post('titulo'),
            'texto' => $this->input->post('texto'),
            'imagen' => $this->input->post('imagen'),
            'thumb' => $this->input->post('thumb'),
            'ciudad' => $this->input->post('ciudad'),
            'publishdate' => $fecha->format('Y-m-d H:i:s'),
            'fecha' => $this->input->post('fecha'),
            'lugar' => $this->input->post('lugar'),
            'status' => $status,
        );

        $this->db->where('id', $id);

        if (!$this->db->update('events', $data)) {
            return false;
        }

        return true;
	}
	
    public function setEventData()
    {
        $status = "0";
        if ($this->input->post('status') == "on") {
            $status = "1";
        }
        $fecha = DateTime::createFromFormat('j F, Y', $this->input->post('publishdate'));
        $data = array(
            'nombre' => $this->input->post('nombre'),
            'titulo' => $this->input->post('titulo'),
            'texto' => $this->input->post('texto'),
            'imagen' => $this->input->post('imagen'),
            'thumb' => $this->input->post('thumb'),
            'ciudad' => $this->input->post('ciudad'),
            'publishdate' => $fecha->format('Y-m-d H:i:s'),
            'fecha' => $this->input->post('fecha'),
            'lugar' => $this->input->post('lugar'),
            'status' => $status,
        );

        if (!$this->db->insert('events', $data)) {
            return false;
        }

    }
}
