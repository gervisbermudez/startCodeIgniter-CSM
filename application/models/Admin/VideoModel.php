<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class VideoModel extends MY_Model
{
    public $table = 'video';
    public $primaryKey = 'video_id';
    public $softDelete = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_categoria($where = [])
    {
        // simple placeholder: return categories from video-categoria relation
        if ($where && isset($where['tipo']) && $where['tipo'] == 'video') {
            // no-op, return empty collection for now
            return new Collection();
        }
        return new Collection();
    }

    public function set_video($data)
    {
        // normalize incoming keys to DB columns and insert, returning insert id
        $insert = [];
        if (isset($data['nombre'])) $insert['nam'] = $data['nombre'];
        if (isset($data['description'])) $insert['description'] = $data['description'];
        if (isset($data['duration'])) $insert['duration'] = $data['duration'];
        if (isset($data['youtubeid'])) $insert['youtube_id'] = $data['youtubeid'];
        if (isset($data['preview'])) $insert['preview'] = $data['preview'];
        if (isset($data['payinfo'])) $insert['payinfo'] = $data['payinfo'];
        if (isset($data['fecha'])) $insert['date_publish'] = $data['fecha'];
        if (isset($data['status'])) $insert['status'] = $data['status'];

        // Use MY_Model helper to insert data so behaviour is consistent across models
        $result = $this->set_data($insert);
        if ($result) {
            $insert_id = $this->db->insert_id();
            return $insert_id ? (int) $insert_id : false;
        }
        return false;
    }

    public function get_video($where = [])
    {
        // Prefer using MY_Model helpers for consistency.
        if (isset($where['id'])) {
            $res = $this->find($where['id']);
            return $res ? $res : false;
        }

        if (isset($where['nombre'])) {
            // campo real en la tabla es 'nam'
            $res = $this->find_with(['nam' => $where['nombre']]);
            return $res ? $res : false;
        }

        // No where provided: return list via all()
        $all = $this->all();
        return $all ? $all : false;
    }

    public function set_video_categoria($data)
    {
        $insert = [];
        if (isset($data['id_video'])) $insert['id_video'] = $data['id_video'];
        if (isset($data['id_categoria'])) $insert['id_categoria'] = $data['id_categoria'];
        $this->db->insert('video-categoria', $insert);
        return $this->db->affected_rows() > 0;
    }

    public function delete_video_categoria($where)
    {
        if (isset($where['id_video'])) {
            $this->db->where('id_video', $where['id_video']);
        }
        $this->db->delete('video-categoria');
        return $this->db->affected_rows() >= 0;
    }

    public function get_video_categoria($where = [])
    {
        $this->db->select('*')->from('video-categoria');
        if (isset($where['id_video'])) {
            $this->db->where('id_video', $where['id_video']);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_video_categoria_rel($where = [])
    {
        // join with categories table if exists; fallback to basic relation
        return $this->get_video_categoria($where);
    }

    public function update_video($where, $data)
    {
        if (!$where || !is_array($where)) {
            return false;
        }

        // Normalize where: accept 'id' as legacy key
        if (isset($where['id'])) {
            $where = ['video_id' => $where['id']];
        }

        // Map incoming data keys to DB columns
        $update = [];
        if (isset($data['nombre'])) $update['nam'] = $data['nombre'];
        if (isset($data['description'])) $update['description'] = $data['description'];
        if (isset($data['duration'])) $update['duration'] = $data['duration'];
        if (isset($data['youtubeid'])) $update['youtube_id'] = $data['youtubeid'];
        if (isset($data['preview'])) $update['preview'] = $data['preview'];
        if (isset($data['payinfo'])) $update['payinfo'] = $data['payinfo'];
        if (isset($data['fecha'])) $update['date_publish'] = $data['fecha'];
        if (isset($data['status'])) $update['status'] = $data['status'];

        return $this->update_data($where, $update);
    }
}
