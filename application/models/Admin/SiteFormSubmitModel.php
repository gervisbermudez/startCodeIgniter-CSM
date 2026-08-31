<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SiteFormSubmitModel extends MY_Model
{
    public $primaryKey = 'siteform_submit_id';
    public $table = 'siteform_submit';
    public $hasOne = [
        'siteform' => ['siteform_id', 'Admin/SiteFormModel', 'SiteFormModel'],
        'user_tracking' => ['user_tracking_id', 'Admin/UserTrackingModel', 'UserTrackingModel'],
    ];
    public $hasData = true;
    public $searchable = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $formIds = array();
        $submitIds = array();
        foreach ($collection as $value) {
            if (isset($value->siteform_id)) {
                $formIds[] = (int) $value->siteform_id;
            }
            if (isset($value->siteform_submit_id)) {
                $submitIds[] = (int) $value->siteform_submit_id;
            }
        }
        $formIds = array_values(array_unique($formIds));
        $submitIds = array_values(array_unique($submitIds));

        $forms = array();
        if (!empty($formIds)) {
            $CI = &get_instance();
            $rows = $CI->db
                ->select('siteform_id, name, template, status, date_create, user_id')
                ->where_in('siteform_id', $formIds)
                ->get('siteform')
                ->result();
            foreach ($rows as $row) {
                $forms[(int) $row->siteform_id] = $row;
            }
        }

        $previews = array();
        if (!empty($submitIds)) {
            $CI = &get_instance();
            $dataRows = $CI->db
                ->where_in('siteform_submit_id', $submitIds)
                ->get('siteform_submit_data')
                ->result();
            $bySubmit = array();
            foreach ($dataRows as $row) {
                $sid = (int) $row->siteform_submit_id;
                if (!isset($bySubmit[$sid])) {
                    $bySubmit[$sid] = array();
                }
                $bySubmit[$sid][$row->_key] = $row->_value;
            }
            foreach ($bySubmit as $sid => $fields) {
                $parts = array();
                foreach ($fields as $value) {
                    if (is_string($value) && $value !== '') {
                        $parts[] = $value;
                    }
                }
                $previews[$sid] = implode(' · ', array_slice($parts, 0, 3));
            }
        }

        foreach ($collection as $value) {
            $formId = isset($value->siteform_id) ? (int) $value->siteform_id : 0;
            $form = isset($forms[$formId]) ? $forms[$formId] : null;
            $value->siteform = $form;
            $value->SiteForm = $form;
            $sid = isset($value->siteform_submit_id) ? (int) $value->siteform_submit_id : 0;
            $value->preview = isset($previews[$sid]) ? $previews[$sid] : '';
        }

        return $collection;
    }
}
