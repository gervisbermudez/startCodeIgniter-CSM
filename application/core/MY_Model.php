<?php

use Tightenco\Collect\Support\Collection;

class MY_model extends CI_Model
{
    public $table;
    public $primaryKey = 'id';
    public $timestamps = true;
    private $map = false;
    public $fields = array();
    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = array();

    public function __construct()
    {
        parent::__construct();
        if (!$this->table) {
            $this->table = strtolower(get_class($this));
        }
    }

    public function getfields()
    {
        return array();
    }

    private function getFieldsSelectCompile()
    {
        $fields = $this->getfields();
        if (count($fields) == 0) {
            return '*';
        } else {
            return implode(", ", $fields);
        }
    }

    public function all()
    {
        $this->db->select($this->getFieldsSelectCompile());
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return new Collection($query->result_array());
        }

        return false;

    }

    /**
     * @param $primaryKey
     */
    public function find($primaryKey)
    {
        $this->db->where(array($this->primaryKey => $primaryKey));
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $result = new Collection($query->result());
            $result = $result->first();
            $this->map = true;
            foreach ($result as $key => $value) {
                $this->fields[] = $key;
                $this->{$key} = $value;
            }
            $this->retrieved();
            return $result;
        }
        return false;
    }

    public function save()
    {
        $this->saving();

        if (!$this->map) {
            $table_fields = $this->db->list_fields($this->table);
        } else {
            $table_fields = $this->fields;
        }
        //Database data
        $data = array();
        //Real table fields in database
        foreach ($table_fields as $key => $value) {
            if (property_exists($this, $value)) {
                $data[$value] = $this->{$value};
            }

            if (!array_key_exists($value, $data)) {
                $data[$value] = null;
                if (array_key_exists($value, $this->attributes)) {
                    $data[$value] = $this->attributes[$value];
                }
            }
        }
        //Update Data or insert data
        if ($this->map) {
            $this->updating();
            $result = $this->update_data(array($this->primaryKey => $this->{$this->primaryKey}), $data);
            $this->updated();

        } else {
            $this->creating();
            $result = $this->set_data($data);
            $this->created();

        }

        return $result;
        $this->saved();

    }

    public function delete()
    {
        if ($this->map) {
            return $this->delete_data(array($this->primaryKey => $this->{$this->primaryKey}));
        }
        return false;
    }

    public function set_data($data)
    {
        if (!$data) {
            return false;
        }
        return $this->db->insert($this->table, $data);
    }

    public function get_data($where = 'all', $limit = '', $order = array('id', 'ASC'))
    {
        $limit ? $this->db->limit($limit) : null;
        if ($order !== '') {
            $this->db->order_by($order[0], $order[1]);
        }
        if ($where === 'all') {
            $query = $this->db->get($this->table);
            if ($query->num_rows() > 0) {
                return new Collection($query->result());
            }
        } else {
            $query = $this->db->get_where($this->table, $where);
            if ($query->num_rows() > 0) {
                return new Collection($query->result());
            }
        }
        return false;
    }

    /**
     * Custom sql query
     */
    public function get_query($sql)
    {
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return new Collection($query->result());
        }
        return false;

    }

    public function delete_data($where)
    {
        if (!$where) {
            return false;
        }
        $this->db->where($where);
        return $this->db->delete($this->table);
    }

    public function get_is_exist_value($field, $value)
    {
        $data = array($field => $value);
        $query = $this->db->get_where($this->table, $data);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function update_data($where, $data)
    {
        $this->db->where($where);
        return $this->db->update($this->table, $data);
    }

    public function get_count($where = false)
    {
        $this->db->from($this->table);
        if ($where) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    /**
     * Returns the last query that was run
     */
    public function last_query()
    {
        return $this->db->last_query();
    }

    /**
     * Events hook model's lifecycle
     */
    public function retrieved()
    {

    }
    public function creating()
    {

    }
    public function created()
    {

    }
    public function updating()
    {

    }
    public function updated()
    {

    }
    public function saving()
    {

    }
    public function saved()
    {

    }
    public function deleting()
    {

    }
    public function deleted()
    {

    }
    public function restoring()
    {

    }
    public function restored()
    {

    }
}
