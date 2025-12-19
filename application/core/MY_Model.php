<?php

use Tightenco\Collect\Support\Collection;

/**Clase MY_Model que extiende de CI_Model e implementa JsonSerializable
 */
class MY_Model extends CI_Model implements JsonSerializable
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
        // Aplicar límite si es que se proporcionó
        if ($limit && is_array($limit)) {
            if (isset($limit[1])) {
                // Aplicar límite con desplazamiento
                $this->db->limit($limit[0], $limit[1]);
            } else {
                // Aplicar límite sin desplazamiento
                $this->db->limit($limit[0]);
            }
        }

        // Aplicar ordenamiento si es que se proporcionó
        if ($order) {
            // Aplicar ordenamiento personalizado
            $this->db->order_by($order[0], $order[1]);
        } else {
            // Aplicar ordenamiento por defecto
            $this->db->order_by($this->primaryKey, 'ASC');
        }

        // Obtener todos los registros si no se especificó una condición
        if ($where === 'all') {
            // Obtener todos los registros de la tabla
            $query = $this->db->get($this->table);
            if ($query->num_rows() > 0) {
                // Devolver una colección de objetos de los registros obtenidos
                return new Collection($query->result());
            }
        } else {
            // Obtener registros que cumplan con la condición proporcionada
            $query = $this->db->get_where($this->table, $where);
            if ($query->num_rows() > 0) {
                // Devolver una colección de objetos de los registros obtenidos
                return new Collection($query->result());
            }
        }

        // Devolver falso si no se encontró ningún registro
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
     * Función para eliminar suavemente un registro de la tabla
     * @param array $where - Condición para la eliminación del registro
     * @return bool - Resultado de la eliminación suave
     */
    public function soft_delete($where)
    {
        // Verifica si la eliminación suave está habilitada
        if (!$this->softDelete) {
            return false;
        }

        // Ejecuta el evento antes de la eliminación suave
        $this->deleting();

        $result = false;

        // Realiza la eliminación suave si el mapeo está habilitado
        if ($this->map) {
            $this->status = 0;

            // Establece la fecha de eliminación si existe una propiedad para ello
            if (property_exists($this, 'date_delete')) {
                $this->date_delete = date("Y-m-d H:i:s");
            }

            // Guarda los cambios en la base de datos
            $result = $this->save();

            // Ejecuta el evento después de la eliminación suave
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
     * created_data() - Crea datos adicionales asociados a un registro en la tabla de datos asociada.
     * @return void
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

    /**
     * Updates the data of the current model in the database.
     * The updated data is stored in a separate table associated with the model's primary table.
     * @return void
     * */
    public function updated_data()
    {
        // Get the name of the data table associated with the current model
        $table_data_name = $this->table . '_data';

        // Get the data to be updated
        $data = $this->{$table_data_name};

        // Get the foreign key of the current model
        $foreing_id = $this->{$this->primaryKey};

        // Loop through each data item and update the corresponding row in the data table
        foreach ($data as $key => $value) {
            $val = $value;

            // Convert objects and arrays to JSON strings
            if (gettype($value) == "object" || gettype($value) == "array") {
                $val = json_encode($value);
            }

            // Build the update query for the data table
            $update = array(
                '_value' => $val,
            );
            $where = array($this->primaryKey => $foreing_id, '_key' => $key);

            // Check if a row with the specified key already exists in the data table
            $this->db->where($where);
            $query = $this->db->get($table_data_name);

            // If a row already exists, update it
            if ($query->num_rows() > 0) {
                $this->db->where($where);
                $this->db->update($table_data_name, $update);
            }
            // If a row doesn't exist, insert a new one
            else {
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

    /**
     * Borra los datos asociados a un registro eliminado
     *
     * @return bool|int Resultado de la eliminación de datos
     */
    public function deleted_data()
    {
        // Nombre de la tabla de datos asociados al registro
        $table_data_name = $this->table . '_data';

        // ID del registro eliminado
        $foreing_id = $this->{$this->primaryKey};

        // Datos a eliminar
        $data = array($this->primaryKey => $foreing_id);

        // Si no hay datos, se devuelve falso
        if (!$data) {
            return false;
        }

        // Se añade la condición de eliminación
        $this->db->where($data);

        // Si no se permite eliminación suave, se borran los datos directamente
        if (!$this->softDelete) {
            return $this->db->delete($table_data_name);
        } else {
            // Si se permite eliminación suave, se actualiza el estado del registro a "eliminado"
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
     * Esta función se encarga de mapear las relaciones de datos de los modelos de CodeIgniter
     * La función mapRelations() se encarga de mapear las relaciones entre las tablas de la base de datos y cargar los datos correspondientes.
     * Primero, verifica si la propiedad map está establecida en true.
     * A continuación, recorre las relaciones hasOne y carga los datos correspondientes de la tabla relacionada utilizando el modelo apropiado. Si el valor delay está establecido en el tercer elemento del array de la relación, se deja el modelo sin cargar.
     * Después, recorre las relaciones hasMany y carga los datos correspondientes de la tabla relacionada utilizando el modelo apropiado. Si el valor delay está establecido en el tercer elemento del array de la relación, se deja el modelo sin cargar. Si hay un cuarto elemento en el array, se fusiona con la cláusula WHERE generada para la consulta.
     * Por último, recorre las relaciones computed y carga los datos resultantes de la función call_user_func().
     */
    public function mapRelations()
    {
        if ($this->map) {
            // Mapeo de relaciones uno a uno
            foreach ($this->hasOne as $key => $value) {
                $this->load->model($value[1]);
                // Se crea una instancia de la clase correspondiente al modelo relacionado
                ${$value[2]} = new $value[2]();
                // Se determina si la carga de los datos de la relación se hace de forma diferida o no
                if (isset($value[3]) && $value[3] == 'delay') {
                    ${$value[2]} = ${$value[2]};
                } else {
                    ${$value[2]}->find($this->{$value[0]});
                }
                // Se establece el objeto mapeado en la propiedad correspondiente del objeto actual
                $this->{$key} = ${$value[2]}->map ? ${$value[2]} : null;
            }

            // Mapeo de relaciones uno a muchos
            foreach ($this->hasMany as $key => $value) {
                $this->load->model($value[1]);
                // Se crea una instancia de la clase correspondiente al modelo relacionado
                ${$key} = new $value[2]();
                // Se determina si la carga de los datos de la relación se hace de forma diferida o no
                if (isset($value[3]) && $value[3] == 'delay') {
                    $this->{$key} = ${$key};
                } else {
                    $where = array($value[0] => $this->{$value[0]});
                    if (isset($value[3]) && is_array($value[3])) {
                        $where = array_merge($where, $value[3]);
                    }
                    // Se establece la condición para recuperar los registros de la relación uno a muchos
                    $this->{$key} = ${$key}->where($where);
                }
            }

            // Mapeo de relaciones computadas
            foreach ($this->computed as $key => $value) {
                // Se llama a la función computada correspondiente para obtener el resultado de la relación
                $this->{$key} = call_user_func(array($this, $value));
            }
        }
        return null;
    }

    public function jsonSerialize()
    {
        if ($this->map) { // Se verifica si la propiedad 'map' es verdadera
            $object = new StdClass(); // Se crea un objeto de la clase StdClass
            // Se itera sobre los campos 'fields' del objeto y se asignan sus valores al objeto StdClass
            foreach ($this->fields as $key => $value) {
                $object->{$value} = $this->{$value};
            }
            // Se itera sobre las relaciones 'hasOne' y se asignan sus valores al objeto StdClass si estos existen en el objeto actual
            foreach ($this->hasOne as $key => $value) {
                if (isset($this->{$key})) {
                    $object->{$key} = $this->{$key};
                }
            }
            // Se itera sobre las relaciones 'hasMany' y se asignan sus valores al objeto StdClass si estos existen en el objeto actual
            foreach ($this->hasMany as $key => $value) {
                if (isset($this->{$key})) {
                    $object->{$key} = $this->{$key};
                }
            }
            // Se itera sobre los campos 'computed' y se asignan sus valores al objeto StdClass si estos existen en el objeto actual
            foreach ($this->computed as $key => $value) {
                if (isset($this->{$key})) {
                    $object->{$key} = $this->{$key};
                }
            }
            // Si la propiedad 'hasData' es verdadera, se crea un nombre de propiedad para los datos del objeto y se asignan estos valores al objeto StdClass
            if ($this->hasData) {
                $object_data_name = $this->table . '_data';
                $object->{$object_data_name} = $this->{$object_data_name};
            }
            return $object; // Se retorna el objeto StdClass serializado en formato JSON
        }
    }

    /**
     * Esta función genera una consulta SELECT en formato JSON para una tabla de la base de datos.
     * La función utiliza la propiedad de la base de datos "list_fields" para obtener todos los campos de la tabla.
     * Luego, se recorren todos los campos para comprobar si son campos protegidos y no se deben incluir en la consulta.
     * Si el campo no está protegido, se construye una cadena que representa el nombre del campo en formato JSON válido.
     * El resultado final es una consulta SELECT que selecciona todos los campos no protegidos de la tabla y los concatena en un objeto JSON utilizando las funciones CONCAT y GROUP_CONCAT de MySQL.
     */
    public function get_select_json($table_name = null)
    {
        // Si no se proporciona un nombre de tabla, se utiliza el nombre de tabla predeterminado.
        $table_name = $table_name ? $table_name : $this->table;

        // Se obtienen los campos de la tabla utilizando la propiedad "list_fields" de la base de datos.
        $fields = $this->db->list_fields($table_name);

        // Se inicializa una cadena vacía que se utilizará para concatenar los nombres de campo en formato JSON.
        $str_fields_names = '';

        // Se recorren todos los campos de la tabla.
        foreach ($fields as $field) {
            // Si el campo no está protegido, se agrega el nombre del campo en formato JSON válido a la cadena de campos.
            if (!in_array($field, $this->protectedFields)) {
                $str_fields_names .= '\'\"' . $field . '\" : \"\',' . $field . ', \'\",\' ';
            }
        }

        // Se busca la última coma en la cadena de nombres de campo y se elimina si existe.
        $pos = strrpos($str_fields_names, ',');
        if ($pos !== false) {
            $str_fields_names = substr_replace($str_fields_names, '', $pos, strlen(','));
        }

        // Se construye la consulta SELECT en formato JSON utilizando las funciones CONCAT y GROUP_CONCAT de MySQL.
        // Los nombres de los campos se encierran entre comillas dobles para que el resultado final sea un objeto JSON válido.
        // La agrupación de los campos se realiza por el identificador único de la fila (campo ID de la tabla).
        return 'SELECT ' . $table_name . '.' . $table_name . '_id' . ', CONCAT(\'{\', GROUP_CONCAT(' . $str_fields_names . '), \'}\')  AS ' . $table_name . ' FROM ' . $table_name . ' GROUP BY ' . $table_name . '.' . $table_name . '_id';
    }

    public function as_data()
    {
        $data = json_encode($this);
        $result = json_decode($data);
        return $result ? $result : new stdClass();
    }

    /**
     * Carga relaciones User de forma optimizada evitando N+1 queries
     * En lugar de hacer 1 query por cada item, hace 1 query total
     * 
     * @param Collection|array $collection Colección de objetos
     * @param string $userIdField Nombre del campo que contiene el user_id
     * @return Collection|array Colección con usuarios cargados
     */
    protected function loadUsersRelation($collection, $userIdField = 'user_id')
    {
        if (empty($collection)) {
            return $collection;
        }

        // Extraer todos los user_ids únicos de forma eficiente
        $userIds = array_unique(
            array_filter(
                array_map(function($item) use ($userIdField) {
                    return isset($item->{$userIdField}) ? $item->{$userIdField} : null;
                }, is_array($collection) ? $collection : $collection->toArray())
            )
        );

        if (empty($userIds)) {
            return $collection;
        }

        // Obtener instancia de CI
        $CI =& get_instance();
        
        // Cargar todos los users en una sola query con campos específicos
        $usersQuery = $CI->db
            ->where_in('user_id', $userIds)
            ->get('user');
        
        // Indexar users por ID para acceso O(1)
        $usersById = [];
        if ($usersQuery->num_rows() > 0) {
            foreach ($usersQuery->result() as $user) {
                $usersById[$user->user_id] = $user;
            }
        }

        // Asignar users a cada item de forma eficiente
        foreach ($collection as &$item) {
            if (isset($item->{$userIdField}) && isset($usersById[$item->{$userIdField}])) {
                $item->user = $usersById[$item->{$userIdField}];
            }
        }

        return $collection;
    }

    /**
     * Carga relaciones File de forma optimizada evitando N+1 queries
     * 
     * @param Collection|array $collection Colección de objetos
     * @param string $fileIdField Nombre del campo que contiene el file_id
     * @param string $targetField Nombre del campo donde se guardará el resultado
     * @return Collection|array Colección con archivos cargados
     */
    protected function loadFilesRelation($collection, $fileIdField = 'mainImage', $targetField = 'imagen_file')
    {
        if (empty($collection)) {
            return $collection;
        }

        // Extraer todos los file_ids únicos de forma eficiente
        $fileIds = array_unique(
            array_filter(
                array_map(function($item) use ($fileIdField) {
                    return isset($item->{$fileIdField}) ? $item->{$fileIdField} : null;
                }, is_array($collection) ? $collection : $collection->toArray())
            )
        );

        if (empty($fileIds)) {
            return $collection;
        }

        // Obtener instancia de CI
        $CI =& get_instance();
        
        // Cargar todos los files en una sola query
        $CI->load->model('Admin/FileModel');
        $filesQuery = $CI->db
            ->where_in('file_id', $fileIds)
            ->get('file');
        
        // Indexar files por ID para acceso O(1)
        $filesById = [];
        if ($filesQuery->num_rows() > 0) {
            foreach ($filesQuery->result() as $file) {
                $filesById[$file->file_id] = $file;
            }
        }

        // Asignar files a cada item con file_front_path
        foreach ($collection as &$item) {
            if (isset($item->{$fileIdField}) && isset($filesById[$item->{$fileIdField}])) {
                $file = new FileModel();
                $file->mapfields((array)$filesById[$item->{$fileIdField}]);
                $fileData = $file->as_data();
                $fileData->file_front_path = $file->getFileFrontPath();
                $item->{$targetField} = $fileData;
            }
        }

        return $collection;
    }

    /**
     * Decodifica campos JSON en una colección de forma eficiente
     * 
     * @param Collection|array $collection Colección de objetos
     * @param array $fields Array de nombres de campos a decodificar (ej: ['json_content', 'metadata'])
     * @return Collection|array Colección con campos JSON decodificados
     */
    protected function decodeJsonFields($collection, $fields = [])
    {
        if (empty($collection) || empty($fields)) {
            return $collection;
        }

        foreach ($collection as &$item) {
            foreach ($fields as $field) {
                if (isset($item->{$field}) && is_string($item->{$field})) {
                    $item->{$field} = json_decode($item->{$field});
                }
            }
        }

        return $collection;
    }

    /**
     * Método helper para cargar múltiples relaciones de una vez
     * Simplifica el código en filter_results
     * 
     * @param Collection|array $collection Colección de objetos
     * @param array $relations Array de configuraciones de relaciones
     * @return Collection|array Colección con relaciones cargadas
     */
    protected function loadRelations($collection, $relations = [])
    {
        foreach ($relations as $relationType => $config) {
            switch ($relationType) {
                case 'user':
                    $userIdField = isset($config['field']) ? $config['field'] : 'user_id';
                    $collection = $this->loadUsersRelation($collection, $userIdField);
                    break;
                
                case 'file':
                    $fileIdField = isset($config['field']) ? $config['field'] : 'mainImage';
                    $targetField = isset($config['target']) ? $config['target'] : 'imagen_file';
                    $collection = $this->loadFilesRelation($collection, $fileIdField, $targetField);
                    break;
            }
        }

        return $collection;
    }

    public function filter_results($collection = [])
    {
        return $collection;
    }
}
