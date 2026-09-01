<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFormsController extends MY_Controller
{
    public $routes_permisions = [
        "index" => [
            "patern" => '/^admin\/siteforms\/?$/',
            "required_permissions" => ["SELECT_SITEFORMS"],
            "conditions" => [],
        ],
        "nuevo" => [
            "patern" => '/^admin\/siteforms\/(nuevo|new)/',
            "required_permissions" => ["CREATE_SITEFORM"],
            "conditions" => [],
        ],
        "editar" => [
            "patern" => '/^admin\/siteforms\/(editar|edit)\/(\d+)/',
            "required_permissions" => ["UPDATE_SITEFORM"],
            "conditions" => [],
        ],
        "submit" => [
            "patern" => '/^admin\/siteforms\/submit/',
            "required_permissions" => ["SELECT_SITEFORMS"],
            "conditions" => [],
        ],
        "export" => [
            "patern" => '/^admin\/siteforms\/export/',
            "required_permissions" => ["SELECT_SITEFORMS"],
            "conditions" => [],
        ],
        "stats" => [
            "patern" => '/^admin\/siteforms\/stats/',
            "required_permissions" => ["SELECT_SITEFORMS"],
            "conditions" => [],
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->check_permisions();
        $this->load->model('Admin/SiteFormModel');
        $this->load->model('Admin/SiteFormSubmitModel');
    }

    public function index()
    {
        $this->renderAdminView('admin.siteforms.siteforms_list', lang('menu_siteforms'), lang('siteforms_all'));
    }

    public function nuevo()
    {
        $this->renderAdminView('admin.siteforms.new_form', lang('menu_siteforms'), lang('siteforms_new'), [
            'siteform_id' => '',
            'editMode' => 'new',
        ]);
    }

    public function editar($siteform_id)
    {
        $this->renderAdminView('admin.siteforms.new_form', lang('menu_siteforms'), lang('siteforms_edit'), [
            'siteform_id' => $siteform_id,
            'editMode' => 'edit',
        ]);
    }

    public function submit()
    {
        $this->renderAdminView('admin.siteforms.siteform_submit_list', lang('menu_siteforms'), lang('siteforms_inbox'));
    }

    /**
     * Export submissions to CSV.
     * GET /admin/siteforms/export/{siteform_id}
     */
    public function export($siteform_id = null)
    {
        $siteform_id = (int) $siteform_id;
        if (!$siteform_id) {
            show_error(lang('siteforms_export_missing'), 400);
            return;
        }

        $siteform = new SiteFormModel();
        if (!$siteform->find($siteform_id)) {
            show_error(lang('siteforms_not_found'), 404);
            return;
        }

        $submissions = $this->db
            ->select('siteform_submit.siteform_submit_id, siteform_submit.date_create, user_tracking.client_ip')
            ->from('siteform_submit')
            ->join('user_tracking', 'user_tracking.user_tracking_id = siteform_submit.user_tracking_id', 'left')
            ->where('siteform_submit.siteform_id', $siteform_id)
            ->where_in('siteform_submit.status', array(1, 2))
            ->order_by('siteform_submit.date_create', 'DESC')
            ->get()
            ->result();

        $allKeys = array();
        $itemRows = $this->db
            ->select('item_name')
            ->from('siteform_items')
            ->where('siteform_id', $siteform_id)
            ->where('status !=', 0)
            ->order_by('siteform_items.order', 'ASC')
            ->get()
            ->result();
        foreach ($itemRows as $item) {
            if (!empty($item->item_name)) {
                $allKeys[$item->item_name] = true;
            }
        }

        $bySubmit = array();
        if (!empty($submissions)) {
            $submitIds = array();
            foreach ($submissions as $submission) {
                $submitIds[] = (int) $submission->siteform_submit_id;
            }
            $dataRows = $this->db
                ->where_in('siteform_submit_id', $submitIds)
                ->get('siteform_submit_data')
                ->result();
            foreach ($dataRows as $row) {
                $sid = (int) $row->siteform_submit_id;
                if (!isset($bySubmit[$sid])) {
                    $bySubmit[$sid] = array();
                }
                $bySubmit[$sid][$row->_key] = $row->_value;
                $allKeys[$row->_key] = true;
            }
        }

        $allKeys = array_keys($allKeys);
        $headers = array_merge(array('ID', 'Fecha', 'IP'), $allKeys);
        $csv_data = array($headers);

        foreach ($submissions as $submission) {
            $sid = (int) $submission->siteform_submit_id;
            $fields = isset($bySubmit[$sid]) ? $bySubmit[$sid] : array();
            $row = array(
                $sid,
                $submission->date_create,
                $submission->client_ip ? $submission->client_ip : '',
            );
            foreach ($allKeys as $key) {
                $row[] = isset($fields[$key]) ? $fields[$key] : '';
            }
            $csv_data[] = $row;
        }

        $filename = 'form_' . $this->sanitize_export_name($siteform->name) . '_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach ($csv_data as $row) {
            fputcsv($output, $row, ';');
        }
        fclose($output);

        system_logger('siteforms', $siteform->siteform_id, 'export_submissions', 'Exportados ' . count($submissions) . ' envíos del formulario: ' . $siteform->name);
        exit;
    }

    /**
     * JSON stats for a form.
     * GET /admin/siteforms/stats/{siteform_id}
     */
    public function stats($siteform_id = null)
    {
        $siteform_id = (int) $siteform_id;
        if (!$siteform_id) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => lang('siteforms_export_missing'),
                )));
            return;
        }

        $siteform = new SiteFormModel();
        if (!$siteform->find($siteform_id)) {
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => lang('siteforms_not_found'),
                )));
            return;
        }

        $this->db->select('
            COUNT(*) as total_submissions,
            MIN(date_create) as first_submission,
            MAX(date_create) as last_submission
        ');
        $this->db->from('siteform_submit');
        $this->db->where('siteform_id', $siteform_id);
        $this->db->where_in('status', array(1, 2));
        $stats = $this->db->get()->row();

        $this->db->select('COUNT(DISTINCT user_tracking.client_ip) as unique_ips');
        $this->db->from('siteform_submit');
        $this->db->join('user_tracking', 'user_tracking.user_tracking_id = siteform_submit.user_tracking_id', 'left');
        $this->db->where('siteform_submit.siteform_id', $siteform_id);
        $this->db->where_in('siteform_submit.status', array(1, 2));
        $ipStats = $this->db->get()->row();

        $this->db->select('DATE(date_create) as date, COUNT(*) as count');
        $this->db->from('siteform_submit');
        $this->db->where('siteform_id', $siteform_id);
        $this->db->where_in('status', array(1, 2));
        $this->db->where('date_create >=', date('Y-m-d', strtotime('-30 days')));
        $this->db->group_by('DATE(date_create)');
        $this->db->order_by('date', 'ASC');
        $daily_submissions = $this->db->get()->result();

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => true,
                'data' => array(
                    'siteform_id' => $siteform_id,
                    'form_name' => $siteform->name,
                    'total_submissions' => $stats ? $stats->total_submissions : 0,
                    'unique_ips' => $ipStats ? $ipStats->unique_ips : 0,
                    'first_submission' => $stats ? $stats->first_submission : null,
                    'last_submission' => $stats ? $stats->last_submission : null,
                    'daily_submissions' => $daily_submissions,
                ),
            )));
    }

    /**
     * @param string $filename
     * @return string
     */
    private function sanitize_export_name($filename)
    {
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        return strtolower($filename);
    }

}
