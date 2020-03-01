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
        /**
         * Set the table name if is null
         */
        if (!$this->table) {
            $this->table = strtolower(get_class($this));
        }
    }

    /**
     * Return all records found on a table or false if nothing is found 
     * @return Collection
     */
    public function all()
    {
        $this->db->select($this->getFieldsSelectCompile());
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            return new Collection($query->result());
        }

        return false;
    }

    /**
     * Search the primary key value specify into the table and map / fill the object with the properties founds 
     * @param string $primaryKey
     * @return array or false on fail
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

    public function where($where)
    {
        $this->db->where($where);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $result = new Collection($query->result());            
            $this->retrieved();
            return $result;
        }
        return false;
    }

    /**
     * Set current object data on database, this method will performs a insert or update sql depends of 
     * map atribute
     * @return boolean 
     */
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

        if($result){
            $this->saved();
        }
        return $result;
    }

    /**
     * Delete the current row map on instance
     * @return boolean on success
     */
    public function delete()
    {
        $this->deleting();
        $result = false;
        if ($this->map) {
            $result =  $this->delete_data(array($this->primaryKey => $this->{$this->primaryKey}));
            if($result){
                $this->deleted();
            }
        }
        
        return $result;
    }

    /**
     * Insert a record into the table
     * @param array associative with fields/data values
     * @return boolean on success
     */
    public function set_data($data)
    {
        if (!$data) {
            return false;
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Performs a sql query on table a returns a Collection instance
     * @param array $where or 'all' for ignore the where clause
     * @param string $limit for set the limit clause on sql
     * @param array $order for set the order clause
     * @return Collection instance
     */
    public function get_data($where = 'all', $limit = '', $order = array())
    {
        $limit ? $this->db->limit($limit) : null;

        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        }else{
            $this->db->order_by($this->primaryKey, 'ASC');
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
     * Performs a custom sql query and returns a Collection instance
     * @param string $sql to exec
     * @return Collection instance
     */
    public function get_query($sql)
    {
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return new Collection($query->result());
        }
        return false;
    }

    /**
     * Performs a sql delete over the table
     * @param array $where
     * @return boolean for success
     */
    public function delete_data($where)
    {
        if (!$where) {
            return false;
        }
        $this->db->where($where);
        return $this->db->delete($this->table);
    }

    /**
     * Performs a sql update over the table
     * @see https://codeigniter.com/user_guide/database/query_builder.html#looking-for-specific-data
     * @param array $where conditional sql where
     * @param array data to update into database
     * @return boolean for success
     */
    public function update_data($where, $data)
    {
        $this->db->where($where);
        return $this->db->update($this->table, $data);
    }

    /**
     * Retuns the numbers of records on the table or sql query
     * @param array $where default false
     * @return int 
     */
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
     * Default getfields function
     * @return array 
     */
    private function getfields()
    {
        return array();
    }

    /**
     * Returns a sql select segment with the table fields mappeds
     * @return string select segment 
     */
    private function getFieldsSelectCompile()
    {
        $fields = $this->getfields();
        if (count($fields) == 0) {
            return '*';
        } else {
            return implode(", ", $fields);
        }
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
