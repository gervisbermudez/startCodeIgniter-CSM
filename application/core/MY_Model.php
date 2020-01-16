<?php 
class MY_model extends CI_Model
{

	function __construct()
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

	public function get_data($where = 'all', $strTable, $limit = '', $order = array('id', 'ASC'))
	{
		$limit ? $this->db->limit($limit) : null;
		if ($order !== '') {
			$this->db->order_by($order[0], $order[1]);
		}
		if ($where === 'all') {
			$query = $this->db->get($strTable);
			if ($query->num_rows() > 0) {
				return $query->result_array();
			}
		} else {
			$query = $this->db->get_where($strTable, $where);
			if ($query->num_rows() > 0) {
				return $query->result_array();
			}
		}
		return false;
	}

	public function get_query($query)
	{
		$query = $this->db->query($query);
		return $query->result_array();
	}

	public function delete_data($where, $strTable)
	{
		if (!$where) {
			return false;
		}
		$this->db->where($where);
		return $this->db->delete($strTable);
	}

	public function get_is_exist_value($table, $field, $value)
	{
		$data = array($field => $value);
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

	public function get_count($table, $where = false)
	{
		$this->db->from($table);
		if ($where) {
			$this->db->where($where);
		}
		return $this->db->count_all_results();
	}
}
?>