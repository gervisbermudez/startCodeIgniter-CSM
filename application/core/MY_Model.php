<?php

use Tightenco\Collect\Support\Collection;

class MY_model extends CI_Model implements JsonSerializable
{
    public $table;
    public $primaryKey = 'id';
    public $timestamps = true;
    public $map = false;
    public $fields = array();
    public $hasData = false;
    public $hasOne = [];
    public $hasMany = [];
    public $protectedFields = array();
    public $computed = array();
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
            $this->mapfields($result);
            $this->retrieved();
            return $result;
        }
        return false;
    }

    /**
     * Search the primary key value specify into the table and map / fill the object with the properties founds
     * @param string $primaryKey
     * @return array or false on fail
     */
    public function find_with($where)
    {
        $this->db->where($where);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $result = new Collection($query->result());
            $result = $result->first();
            $this->mapfields($result);
            $this->retrieved();
            return $result;
        }
        return false;
    }

    public function mapfields($fields)
    {
        $this->before_map();
        $this->map = true;
        foreach ($fields as $key => $value) {
            if (!in_array($key, $this->protectedFields)) {
                $this->fields[] = $key;
                $this->{$key} = $value;
            }
        }
        $this->after_map();
    }

    public function where($where)
    {
        $this->db->where($where);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $result = new Collection($query->result());
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
            if ($result) {
                $this->updated();
            }

        } else {
            $this->creating();
            $result = $this->set_data($data);
            if ($result) {
                $this->{$this->primaryKey} = $this->db->insert_id();
                $this->created();
            }
        }

        if ($result) {
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
            $result = $this->delete_data(array($this->primaryKey => $this->{$this->primaryKey}));
            if ($result) {
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
        } else {
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

    public function get_primary()
    {
        return $this->{$this->primaryKey};
    }

    /**
     * Events for table data
     */
    public function retrieved_data()
    {
        $foreing_id = $this->{$this->primaryKey};
        $table_data_name = $this->table . '_data';
        $sql = "SELECT d." . $this->primaryKey . ", CONCAT('{', GROUP_CONCAT('\"', d._key, '\"', ':', '\"', d._value, '\"'), '}')
				AS `data` FROM " . $table_data_name . " d
				WHERE " . $this->primaryKey . " = $foreing_id
				GROUP BY " . $this->primaryKey;
        $table_data = $this->get_query($sql);
        if ($table_data) {
            $table_data = json_decode($table_data->first()->data);
        }
        $this->{$table_data_name} = $table_data ? $table_data : [];
    }

    public function created_data()
    {
        $table_data_name = $this->table . '_data';
        $foreing_id = $this->{$this->primaryKey};
        $data = $this->{$table_data_name} ? $this->{$table_data_name} : [];
        foreach ($data as $key => $value) {
            $insert = array(
                $this->primaryKey => $foreing_id,
                '_key' => $key,
                '_value' => $value,
                'status' => 1,
            );
            $this->db->insert($table_data_name, $insert);
        }
        $this->find($this->user_id);
    }

    public function updated_data()
    {
        $table_data_name = $this->table . '_data';
        $data = $this->{$table_data_name};
        $foreing_id = $this->{$this->primaryKey};
        foreach ($data as $key => $value) {
            $update = array(
                '_value' => $value,
            );
            $where = array($this->primaryKey => $foreing_id, '_key' => $key);
            $this->db->where($where);
            $this->db->update($table_data_name, $update);
        }
    }

    public function deleted_data()
    {
        $table_data_name = $this->table . '_data';
        $foreing_id = $this->{$this->primaryKey};
        $data = array($this->primaryKey => $foreing_id);
        if (!$data) {
            return false;
        }
        $this->db->where($data);
        return $this->db->delete($table_data_name);
    }

    /**
     * Events hook model's lifecycle
     */

    public function before_map()
    {
    }

    public function after_map()
    {
        $this->mapRelations();
    }

    public function retrieved()
    {
        if ($this->hasData) {
            $this->retrieved_data();
        }
    }

    public function creating()
    {

    }

    public function created()
    {
        if ($this->hasData) {
            $this->created_data();
        }
    }

    public function updating()
    {

    }

    public function updated()
    {
        if ($this->hasData) {
            $this->updated_data();
        }
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
        if ($this->hasData) {
            $this->deleted_data();
        }
    }

    public function restoring()
    {

    }
    
    public function restored()
    {

    }

    /**
     * Relations data events
     */
    public function mapRelations()
    {
        if ($this->map) {
            foreach ($this->hasOne as $key => $value) {
                $this->load->model($value[1]);
                ${$value[2]} = new $value[2]();
                ${$value[2]}->find($this->{$value[0]});
                $this->{$key} = ${$value[2]};
            }
            foreach ($this->hasMany as $key => $value) {
                $this->load->model($value[1]);
                ${$value[2]} = new $value[2]();
                $this->{$value[2]} = ${$value[2]}->where(array($value[0] => $this->{$value[0]}));
            }
            foreach ($this->computed as $key => $value) {
                $this->{$key} = $this->{$value}();
            }
        }
        return null;
    }

    public function jsonSerialize()
    {
        if ($this->map) {
            $object = new StdClass();
            foreach ($this->fields as $key => $value) {
                $object->{$value} = $this->{$value};
            }
            foreach ($this->hasOne as $key => $value) {
                if (isset($this->{$key})) {
                    $object->{$key} = $this->{$key};
                }
            }
            foreach ($this->computed as $key => $value) {
                if (isset($this->{$key})) {
                    $object->{$key} = $this->{$key};
                }
            }
            if ($this->hasData) {
                $object_data_name = $this->table . '_data';
                $object->{$object_data_name} = $this->{$object_data_name};
            }
            return $object;
        }
    }

    public function get_select_json($table_name = null)
    {
        $table_name = $table_name ? $table_name : $this->table;
        $fields = $this->db->list_fields($table_name);
        $str_fields_names = '';
        foreach ($fields as $field) {
            if (!in_array($field, $this->protectedFields)) {
                $str_fields_names .= '\'\"' . $field . '\" : \"\',' . $field . ', \'\",\' ';
            }
        }
        $pos = strrpos($str_fields_names, ',');

        if ($pos !== false) {
            $str_fields_names = substr_replace($str_fields_names, '', $pos, strlen(','));
        }

        return 'SELECT ' . $table_name . '.' . $table_name . '_id' . ', CONCAT(\'{\', GROUP_CONCAT(' . $str_fields_names . '), \'}\')  AS ' . $table_name . ' FROM ' . $table_name . ' GROUP BY ' . $table_name . '.' . $table_name . '_id';
    }
}
