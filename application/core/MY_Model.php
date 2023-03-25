<?php

use Tightenco\Collect\Support\Collection;

/**Clase MY_model que extiende de CI_Model e implementa JsonSerializable
 */
class MY_model extends CI_Model implements JsonSerializable
{
    /** Nombre de la tabla en la base de datos
     */
    public $table;
    /**Nombre de la clave primaria de la tabla
     */
    public $primaryKey = 'id';
    /**Indica si la tabla tiene timestamps (created_at y updated_at)
     */
    public $timestamps = true;
    /**Indica si la tabla tiene soft deletes (deleted_at)
     */
    public $softDelete = false;
    /**Indica si se va a mapear la salida de los campos
     */
    public $map = false;
    /**Campos de la tabla
     */
    public $fields = array();
    /**Indica si hay datos cargados en el modelo
     */
    public $hasData = false;
    /**Relación hasOne con otras tablas
     */
    public $hasOne = [];
    /**Relación hasMany con otras tablas
     */
    public $hasMany = [];
    /**Campos protegidos que no se pueden modificar
     */
    public $protectedFields = array();
    /**Campos calculados que no se guardan en la base de datos
     */
    public $computed = array();
    /**Permisos que tiene el modelo
     */
    public $permisions = array(
        "CREATE",
        "UPDATE",
        "DELETE",
        "SELECT",
    );
    /**Los valores predeterminados de los atributos del modelo.
    @var array
     */
    protected $attributes = array();
    /**Constructor de la clase */
    public function __construct()
    {
        parent::__construct();
        /*
        Si no se ha definido el nombre de la tabla, se utiliza el nombre de la clase en minúsculas
         */
        if (!$this->table) {
            $this->table = strtolower(get_class($this));
        }
    }

    /**
     * Obtiene todos los registros de la tabla actual de la base de datos.
     * Return all records found on a table or false if nothing is found
     *
     * @param array $limit Un array que contiene límites de registros y desplazamientos.
     * @param array $order Un array que contiene el campo y la dirección de ordenamiento.
     * @return Collection|bool Retorna una colección de objetos de la clase que hereda de esta, o false si no hay resultados.
     */
    public function all($limit = array(), $order = array())
    {
        // Aplica limit si se especifica
        if ($limit && is_array($limit)) {
            if (isset($limit[1])) {
                $this->db->limit($limit[0], $limit[1]);
            } else {
                $this->db->limit($limit[0]);
            }
        }

        // Aplica ordenamiento si se especifica
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by($this->primaryKey, 'ASC');
        }

        // Selecciona los campos a obtener
        $this->db->select($this->getFieldsSelectCompile());

        // Filtra los registros que tienen un campo de estado igual a 1
        $this->db->where(array('status' => 1));

        // Obtiene los resultados
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {

            // Devuelve una colección de objetos de la clase que hereda de esta
            return new Collection($this->filter_results($query->result()));
        }

        // Devuelve false si no hay resultados
        return false;
    }

    /**
     * Método que devuelve una lista de elementos paginados utilizando el método all()
     * @return Collection|bool Lista de elementos paginados
     * */
    public function pager()
    {
        // Obtiene la información de paginación
        $pagination_info = $this->get_pagination_info();

        // Establece los límites de la consulta
        $limit = [$pagination_info["per_page"], $pagination_info["offset"]];

        // Retorna la lista de elementos obtenidos mediante el método all()
        return $this->all($limit);
    }

    /**
     * Obtiene información de paginación para una lista de resultados.
     *
     * @return array Información de paginación.
     */
    public function get_pagination_info()
    {
        // Verifica si el parámetro 'page' está presente en la URL.
        if (isset($_GET['page'])) {
            $current_page = $_GET['page'];
        } else {
            $current_page = 1;
        }
        $per_page = 25; // Número de resultados por página.
        $total_rows = $this->get_count_all(); // Número total de resultados.
        $offset = (($current_page - 1) * $per_page) + 1; // Registro de inicio en la página actual.
        $total_pages = ceil($total_rows / $per_page); // Número total de páginas.

        // Devuelve un arreglo con la información de paginación.
        return [
            "current_page" => $current_page,
            "per_page" => $per_page,
            "total_rows" => $total_rows,
            "offset" => $offset,
            "total_pages" => $total_pages,
            "first_page" => 1,
            "last_page" => $total_pages,
            "next_page" => $current_page + 1,
            "prev_page" => $current_page - 1,
        ];
    }

    public function get_count_all()
    {
        return $this->db->count_all($this->table);
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

    /**
     * Mapea los campos de la tabla con los campos del modelo.
     * @param array $fields Arreglo con los campos a ser mapeados.
     * @return void
     * */
    public function mapfields($fields)
    {
        $this->before_map(); //Método que se ejecuta antes del mapeo de campos
        $this->map = true; //Indica que se está haciendo el mapeo de campos
        foreach ($fields as $key => $value) {
            if (!in_array($key, $this->protectedFields)) {
                $this->fields[] = $key; //Agrega el campo a la lista de campos del modelo
                $this->{$key} = $value; //Asigna el valor del campo al atributo correspondiente del modelo
            }
        }
        $this->after_map(); //Método que se ejecuta después del mapeo de campos
    }

    /**
     * Método "where" para agregar cláusulas "WHERE" a una consulta de base de datos en CodeIgniter.
     * @param string|array $where - Cláusula "WHERE" en forma de cadena o matriz asociativa.
     * @param string|array $limit - Límite de resultados. Puede ser un número o una matriz con el número y la posición de inicio.
     * @param array $order - Orden de los resultados por columna.
     * @return Collection|false - Una colección de objetos si hay resultados, de lo contrario devuelve "false".
     * */
    public function where($where, $limit = '', $order = array())
    {

        // Agregar límite a la consulta si se proporciona.
        if ($limit) {
            if (is_array($limit)) {
                isset($limit[1]) ? $this->db->limit($limit[0], $limit[1]) : $this->db->limit($limit[0]);
            } else {
                $this->db->limit($limit);
            }
        }

        // Agregar orden a la consulta si se proporciona, de lo contrario, ordenar por la clave primaria en orden ascendente.
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by($this->primaryKey, 'ASC');
        }

        // Agregar cláusula "WHERE" a la consulta.
        $this->db->where($where);

        // Ejecutar la consulta.
        $query = $this->db->get($this->table);

        // Si hay resultados, crear una colección de objetos y devolverla.
        if ($query->num_rows() > 0) {
            $result = new Collection($this->filter_results($query->result()));
            return $result;
        }
        // De lo contrario, devolver "false".
        return false;
    }

    /**
     * @param str_term string
     * @return Collection
     */
    /**
     * Busca un término dado en todos los campos de una tabla en la base de datos y devuelve una colección de resultados.
     * @param str_term string - Término a buscar
     * @return Collection - Una colección de resultados de búsqueda
     **/
    public function search($str_term)
    {
        // Seleccionar todos los campos de la tabla
        $this->db->select('');
        // Especificar la tabla
        $this->db->from($this->table);

        // Obtener los nombres de todos los campos de la tabla
        $table_fields = $this->db->list_fields($this->table);

        // Bucle a través de los campos de la tabla y agregar una cláusula LIKE a cada uno de ellos
        foreach ($table_fields as $key => $value) {
            $this->db->or_like($value, $str_term);
        }

        // Ejecutar la consulta y devolver los resultados como una colección
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = new Collection($this->filter_results($query->result()));
            return $result;
        }

        // Si no se encuentran resultados, devolver una colección vacía
        return new Collection([]);
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
        if ($limit && is_array($limit)) {
            if (isset($limit[1])) {
                $this->db->limit($limit[0], $limit[1]);
            } else {
                $this->db->limit($limit[0]);
            }
        }

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
        foreach ($data as $key => $value) {
            if (gettype($value) == "object" || gettype($value) == "array") {
                $data[$key] = json_encode($value);
            }

        }
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

    /**
     * Busca los datos de la tabla "table_data" relacionados con un campo de clave primaria específico y lo devuelve como un array
     * @param string $primaryKey El valor de la clave primaria para buscar
     * @param string $primaryKeyFieldName El nombre del campo de la clave primaria
     * @return array Un array con los datos de la tabla "table_data" correspondientes a la clave primaria especificada
     * */
    public function search_for_data($primaryKey, $primaryKeyFieldName)
    {
        $table_data_name = $this->table . '_data';

        // Realiza una consulta para obtener los datos de la tabla "table_data" que se relacionan con la clave primaria especificada
        $this->db->where(array($primaryKeyFieldName => $primaryKey));
        $query = $this->db->get($table_data_name);
        $temp_array = [];

        // Si la consulta devuelve resultados, procesa cada resultado y los agrega a un array temporal
        if ($query->num_rows() > 0) {
            $table_data = new Collection($query->result());
            foreach ($table_data as $value) {
                $decode_value = json_decode($value->{"_value"});
                if (gettype($decode_value) == "object" || gettype($decode_value) == "array") {
                    $temp_array[$value->{"_key"}] = $decode_value;
                } else {
                    $temp_array[$value->{"_key"}] = $value->{"_value"};
                }
            }
        }

        // Si el array temporal contiene datos, devuelve el array, de lo contrario devuelve un array vacío
        return $temp_array ? $temp_array : [];
    }

    /**
     * created_data() - Crea datos adicionales asociados a un registro en la tabla de datos asociada. @return void
     */
    public function created_data()
    {
        // Se obtiene el nombre de la tabla de datos.
        $table_data_name = $this->table . '_data';

        // Se obtiene el valor de la llave foránea correspondiente al registro.
        $foreing_id = $this->{$this->primaryKey};

        // Se obtiene los datos adicionales del registro, si existen.
        $data = isset($this->{$table_data_name}) ? $this->{$table_data_name} : [];

        // Se itera sobre los datos adicionales para insertarlos en la tabla de datos.
        foreach ($data as $key => $value) {

            // Si el valor es un objeto o un arreglo, se convierte a formato JSON.
            if (gettype($value) == "object" || gettype($value) == "array") {
                $value = json_encode($value);
            }

            // Se crea el arreglo con los datos a insertar.
            $insert = array(
                $this->primaryKey => $foreing_id,
                '_key' => $key,
                '_value' => $value,
                'status' => 1,
            );

            // Se insertan los datos adicionales en la tabla de datos.
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
                $this->{$key} = call_user_func(array($this, $value));
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