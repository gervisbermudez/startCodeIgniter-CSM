<?php
/**
 * The standar model
 */
class StModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function set_data($data, $strTable)
    {
        if (!$data) {
            return false;
        }
        return $this->db->insert($strTable, $data);
    }

    public function get_data($data = 'all', $strTable, $limit = '', $order = array('id', 'DESC'))
    {
        $limit ? $this->db->limit($limit) : null;
        if ($order !== '') {
            $this->db->order_by($order[0], $order[1]);
        }
        if ($data === 'all') {
            $query = $this->db->get($strTable);
            if ($query->num_rows() > 0) {
                return $query->result_array();
            }
        } else {
            $query = $this->db->get_where($strTable, $data);
            if ($query->num_rows() > 0) {
                return $query->result_array();
            }
        }
        return false;
    }

    public function delete_data($where, $strTable)
    {
        if (!$where) {
            return false;
        }
        $this->db->where($where);
        return $this->db->delete($strTable);
    }

    public function get_is_exist_value($table, $campo, $valor)
    {
        if (!$table || !$campo || !$valor) {
            return false;
        }
        $data = array($campo => $valor);
        $query = $this->db->get_where($table, $data);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_data($where, $data, $strTable)
    {
        $this->db->where($where);
        return $this->db->update($strTable, $data);
    }
}
