<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFormsController extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->renderAdminView('admin.siteforms.siteforms_list', 'Formularios del Sitio', 'Todas los Formularios del Sitio');
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.siteforms.new_form', 'Formularios del Sitio', 'Nuevo Formulario', [
            'siteform_id' => '',
            'editMode' => 'new'
        ]);
    }

    public function editar($siteform_id)
    {
        $this->renderAdminView('admin.siteforms.new_form', 'Formularios del Sitio', 'Editar SiteForm', [
            'siteform_id' => $siteform_id,
            'editMode' => 'edit'
        ]);
    }

    public function submit()
    {
        $this->renderAdminView('admin.siteforms.siteform_submit_list', 'Formularios recibidos del Sitio', 'Todas los Formularios del Sitio');
    }
    
    /**
     * Exportar datos de formulario a CSV
     * GET /admin/siteforms/export/{form_name}
     */
    public function export($form_name = null)
    {
        try {
            if (!$form_name) {
                throw new Exception('Nombre de formulario no proporcionado');
            }
            
            // Verificar permisos
            if (!has_permisions('SELECT_SITEFORMS')) {
                throw new Exception('Sin permisos para exportar formularios');
            }
            
            // Decodificar el nombre del formulario
            $form_name = urldecode($form_name);
            
            // Cargar modelo de formularios
            $this->load->model('siteforms_model');
            $this->load->model('siteform_submit_model');
            
            // Verificar que el formulario existe
            $form = $this->siteforms_model->where('form_name', $form_name)->first();
            
            if (!$form) {
                throw new Exception('Formulario no encontrado');
            }
            
            // Obtener todos los envíos del formulario
            $submissions = $this->siteform_submit_model
                ->where('siteform_id', $form->siteform_id)
                ->order_by('created_at', 'DESC')
                ->get_all();
            
            if (empty($submissions)) {
                throw new Exception('No hay envíos para exportar');
            }
            
            // Preparar datos para CSV
            $csv_data = [];
            
            // Obtener todos los campos posibles
            $all_fields = [];
            foreach ($submissions as $submission) {
                $form_data = json_decode($submission->form_data, true);
                if (is_array($form_data)) {
                    $all_fields = array_merge($all_fields, array_keys($form_data));
                }
            }
            $all_fields = array_unique($all_fields);
            
            // Agregar campos adicionales
            $headers = array_merge(['ID', 'Fecha de Envío', 'IP'], $all_fields);
            $csv_data[] = $headers;
            
            // Agregar datos
            foreach ($submissions as $submission) {
                $form_data = json_decode($submission->form_data, true);
                
                $row = [
                    $submission->siteform_submit_id,
                    $submission->created_at,
                    $submission->ip_address ?? 'N/A'
                ];
                
                // Agregar valores de cada campo
                foreach ($all_fields as $field) {
                    $row[] = isset($form_data[$field]) ? $form_data[$field] : '';
                }
                
                $csv_data[] = $row;
            }
            
            // Generar archivo CSV
            $filename = 'form_' . sanitize_filename($form_name) . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $output = fopen('php://output', 'w');
            
            // BOM para UTF-8 (para Excel)
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Escribir datos
            foreach ($csv_data as $row) {
                fputcsv($output, $row, ';'); // Usar punto y coma para Excel en español
            }
            
            fclose($output);
            
            // Log de la acción
            system_logger('siteforms', $form->siteform_id, 'export_submissions', 'Exportados ' . count($submissions) . ' envíos del formulario: ' . $form_name);
            
            exit;
            
        } catch (Exception $e) {
            // Si hay error, mostrar mensaje
            show_error('Error al exportar: ' . $e->getMessage(), 400);
        }
    }
    
    /**
     * Obtener estadísticas de un formulario
     * GET /admin/siteforms/stats/{form_name}
     */
    public function stats($form_name = null)
    {
        try {
            if (!$form_name) {
                throw new Exception('Nombre de formulario no proporcionado');
            }
            
            if (!has_permisions('SELECT_SITEFORMS')) {
                throw new Exception('Sin permisos');
            }
            
            $form_name = urldecode($form_name);
            
            $this->load->model('siteforms_model');
            $this->load->model('siteform_submit_model');
            
            $form = $this->siteforms_model->where('form_name', $form_name)->first();
            
            if (!$form) {
                throw new Exception('Formulario no encontrado');
            }
            
            // Obtener estadísticas
            $this->db->select('
                COUNT(*) as total_submissions,
                COUNT(DISTINCT ip_address) as unique_ips,
                MIN(created_at) as first_submission,
                MAX(created_at) as last_submission
            ');
            $this->db->from('siteform_submit');
            $this->db->where('siteform_id', $form->siteform_id);
            $stats = $this->db->get()->row();
            
            // Envíos por día (últimos 30 días)
            $this->db->select('DATE(created_at) as date, COUNT(*) as count');
            $this->db->from('siteform_submit');
            $this->db->where('siteform_id', $form->siteform_id);
            $this->db->where('created_at >=', date('Y-m-d', strtotime('-30 days')));
            $this->db->group_by('DATE(created_at)');
            $this->db->order_by('date', 'ASC');
            $daily_submissions = $this->db->get()->result();
            
            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => [
                        'form_name' => $form_name,
                        'total_submissions' => $stats->total_submissions,
                        'unique_ips' => $stats->unique_ips,
                        'first_submission' => $stats->first_submission,
                        'last_submission' => $stats->last_submission,
                        'daily_submissions' => $daily_submissions
                    ]
                ]));
                
        } catch (Exception $e) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

}

/**
 * Sanitizar nombre de archivo
 */
function sanitize_filename($filename)
{
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    return strtolower($filename);
}