<?php

use Tightenco\Collect\Support\Collection;

class MY_model extends CI_Model implements JsonSerializable
{
    public $table;
    public $primaryKey = 'id';
    public $timestamps = true;
    public $softDelete = false;
    public $map = false;
    public $fields = array();
    public $hasData = false;
    public $hasOne = [];
    public $hasMany = [];
    public $protectedFields = array();
    public $computed = array();
    public $permisions = array(
        "CREATE",
        "UPDATE",
        "DELETE",
        "SELECT",
    );

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
        $this->db->where(array('status' => 1));
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {

            return new Collection($this->filter_results($query->result()));
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
            $result = new Collection($this->filter_results($query->result()));
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
            $result = $this->db->insert($this->table, $data);
            $insert_id = $this->db->insert_id();
            if ($result) {
                $this->mapfields($data);
                $this->{$this->primaryKey} = $insert_id;
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
        if (!$this->softDelete) {
            $this->deleting();
            $result = false;
            if ($this->map) {
                $result = $this->delete_data(array($this->primaryKey => $this->{$this->primaryKey}));
                if ($result) {
                    $this->deleted();
                }
            }
        } else {
            $result = $this->soft_delete(array($this->primaryKey => $this->{$this->primaryKey}));
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

    public function soft_delete($where)
    {
        if (!$this->softDelete) {
            return false;
        }
        $this->deleting();
        $result = false;
        if ($this->map) {
            $this->status = 0;
            if (property_exists($this, 'date_delete')) {
                $this->date_delete = date("Y-m-d H:i:s");
            }
            $result = $this->save();
            if ($result) {
                $this->deleted();
            }
        }
        return $result;
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
        $table_data_name = $this->table . '_data';
        $this->{$table_data_name} = $this->search_for_data($this->{$this->primaryKey}, $this->primaryKey);
    }

    public function search_for_data($primaryKey, $primaryKeyFieldName)
    {
        $table_data_name = $this->table . '_data';

        $this->db->where(array($primaryKeyFieldName => $primaryKey));
        $query = $this->db->get($table_data_name);
        if ($query->num_rows() > 0) {
            $table_data = new Collection($query->result());
        }
        $temp_array = [];
        foreach ($table_data as $value) {
            $decode_value = json_decode($value->{"_value"});
            if (gettype($decode_value) == "object" || gettype($decode_value) == "array") {
                $temp_array[$value->{"_key"}] = $decode_value;
            } else {
                $temp_array[$value->{"_key"}] = $value->{"_value"};
            }
        }
        return $temp_array ? $temp_array : [];
    }

    public function created_data()
    {
        $table_data_name = $this->table . '_data';
        $foreing_id = $this->{$this->primaryKey};
        $data = $this->{$table_data_name} ? $this->{$table_data_name} : [];
        foreach ($data as $key => $value) {

            if (gettype($value) == "object" || gettype($value) == "array") {
                $value = json_encode($value);
            }

            $insert = array(
                $this->primaryKey => $foreing_id,
                '_key' => $key,
                '_value' => $value,
                'status' => 1,
            );

            $this->db->insert($table_data_name, $insert);

        }
    }

    public function updated_data()
    {
        $table_data_name = $this->table . '_data';
        $data = $this->{$table_data_name};
        $foreing_id = $this->{$this->primaryKey};
        foreach ($data as $key => $value) {
            $val = $value;
            if (gettype($value) == "object" || gettype($value) == "array") {
                $val = json_encode($value);
            }
            $update = array(
                '_value' => $val,
            );
            $where = array($this->primaryKey => $foreing_id, '_key' => $key);
            $this->db->where($where);
            $query = $this->db->get($table_data_name);
            if ($query->num_rows() > 0) {
                $this->db->where($where);
                $this->db->update($table_data_name, $update);
            } else {
                //Try insert if not exit previously
                $insert = array(
                    $this->primaryKey => $foreing_id,
                    '_key' => $key,
                    '_value' => $val,
                    'status' => 1,
                );

                $this->db->insert($table_data_name, $insert);
            }
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
        if (!$this->softDelete) {
            return $this->db->delete($table_data_name);
        } else {
            $this->db->update($table_data_name, ["status" => "0"]);
        }
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
                if (isset($value[3]) && $value[3] == 'delay') {
                    ${$value[2]} = ${$value[2]};
                } else {
                    ${$value[2]}->find($this->{$value[0]});
                }
                $this->{$key} = ${$value[2]}->map ? ${$value[2]} : null;
            }
            foreach ($this->hasMany as $key => $value) {
                $this->load->model($value[1]);
                ${$key} = new $value[2]();
                if (isset($value[3]) && $value[3] == 'delay') {
                    $this->{$key} = ${$key};
                } else {
                    $where = array($value[0] => $this->{$value[0]});
                    if (isset($value[3]) && is_array($value[3])) {
                        $where = array_merge($where, $value[3]);
                    }
                    $this->{$key} = ${$key}->where($where);
                }
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
            foreach ($this->hasMany as $key => $value) {
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

    public function as_data()
    {
        $data = json_encode($this);
        $result = json_decode($data);
        return $result ? $result : new stdClass();
    }

    public function filter_results($collection = [])
    {
        return $collection;
    }
}
