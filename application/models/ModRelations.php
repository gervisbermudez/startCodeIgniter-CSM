<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ModRelations extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert a relation log into `relations` table.
     * Expected keys: user_id, tablename, id_row, action, date (optional)
     */
    public function set_relation($data = [])
    {
        if (!is_array($data)) return false;
        $insert = [];
        $insert['user_id'] = isset($data['user_id']) ? $data['user_id'] : 0;
        $insert['tablename'] = isset($data['tablename']) ? $data['tablename'] : '';
        $insert['id_row'] = isset($data['id_row']) ? $data['id_row'] : 0;
        $insert['action'] = isset($data['action']) ? $data['action'] : '';
        $insert['date'] = isset($data['date']) ? $data['date'] : date('Y-m-d H:i:s');

        $this->db->insert('relations', $insert);
        return $this->db->insert_id() ? (int) $this->db->insert_id() : false;
    }
}
