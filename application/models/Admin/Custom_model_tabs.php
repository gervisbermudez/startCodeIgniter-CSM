<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * La clase Custom_model_tabs extiende de la clase MY_Model.
 * Se define el nombre de la clave primaria de la tabla.
 * Se define una relación de hasMany con la tabla Custom_model_fields.
 */
class Custom_model_tabs extends MY_Model
{
    public $primaryKey = 'custom_model_tab_id';

    public $hasMany = [
        'custom_model_fields' => ['custom_model_tab_id', 'Admin/Custom_model_fields', 'Custom_model_fields'],
    ];

    /**
     * Constructor de la clase.
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Función que filtra los resultados de una colección.
     * Se carga el modelo Custom_model_fields.
     * Se itera sobre la colección y se agregan los campos personalizados de cada registro.
     * @param array $collection - Colección a filtrar.
     * @return array - Colección filtrada.
     */
    public function filter_results($collection = [])
    {
        $this->load->model('Admin/Custom_model_fields');
        foreach ($collection as $key => &$value) {
            if (isset($value->custom_model_tab_id) && $value->custom_model_tab_id) {
                $custom_model_fields = new Custom_model_fields();
                $value->{'custom_model_fields'} = $custom_model_fields->where(['custom_model_tab_id' => $value->custom_model_tab_id]);
            }
        }
        return $collection;
    }
}