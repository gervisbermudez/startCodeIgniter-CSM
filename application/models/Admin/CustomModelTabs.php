<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * La clase CustomModelTabs extiende de la clase MY_Model.
 * Se define el nombre de la clave primaria de la tabla.
 * Se define una relación de hasMany con la tabla CustomModelFields.
 */
class CustomModelTabs extends MY_Model
{
    public $table = 'custom_model_tabs';
    public $primaryKey = 'custom_model_tab_id';

    public $hasMany = [
        'custom_model_fields' => ['custom_model_tab_id', 'Admin/CustomModelFields', 'CustomModelFields'],
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
        $this->load->model('Admin/CustomModelFields');
        foreach ($collection as $key => &$value) {
            if (isset($value->custom_model_tab_id) && $value->custom_model_tab_id) {
                $custom_model_fields = new CustomModelFields();
                $value->{'custom_model_fields'} = $custom_model_fields->where(['custom_model_tab_id' => $value->custom_model_tab_id]);
            }
        }
        return $collection;
    }
}