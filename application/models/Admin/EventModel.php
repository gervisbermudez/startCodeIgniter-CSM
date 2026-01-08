<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class EventModel extends MY_Model
{

    public $primaryKey = 'event_id';
    public $table = "events";
    public $softDelete = true;
    public $computed = ["mainImage" => "mainImage"];
    public $hasOne = [
        'user' => ['user_id', 'Admin/UserModel', 'UserModel'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function mainImage()
    {
        // Si no hay mainImage, retornar null
        if (empty($this->mainImage)) {
            return null;
        }

        $this->load->model('Admin/FileModel');
        $file = new FileModel();
        $file->find($this->mainImage);
        
        // Verificar que el archivo fue encontrado
        if (!$file->{$file->primaryKey}) {
            return null;
        }
        
        $imagen_file = $file->as_data();
        $imagen_file->{'file_front_path'} = new stdClass();
        $imagen_file->{'file_front_path'} = $file->getFileFrontPath();
        return $imagen_file;
    }


    public function filter_results($collection = [])
    {
        // Cargar users y files de forma optimizada usando loadRelations
        return $this->loadRelations($collection, [
            'user' => ['field' => 'user_id'],
            'file' => ['field' => 'mainImage', 'target' => 'imagen_file']
        ]);
    }

    /**
     * Obtiene todos los eventos sin filtrar por status
     * Útil para el panel de administración donde se necesitan ver todos los eventos (publicados y borradores)
     * @return Collection|false
     */
    public function get_all($limit = array(), $order = array())
    {
        // Aplicar límite si se especifica
        if ($limit && is_array($limit)) {
            if (isset($limit[1])) {
                $this->db->limit($limit[0], $limit[1]);
            } else {
                $this->db->limit($limit[0]);
            }
        }

        // Aplicar ordenamiento si se especifica
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by($this->primaryKey, 'DESC'); // Más recientes primero
        }

        // NO filtrar por status - devolver todos los registros
        
        // Obtiene los resultados
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            // Devuelve una colección de objetos
            return new Collection($this->filter_results($query->result()));
        }

        return false;
    }

}
