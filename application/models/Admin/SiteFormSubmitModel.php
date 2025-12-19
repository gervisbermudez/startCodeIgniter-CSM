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

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/SiteFormModel');
        foreach ($collection as $key => &$value) {
            if (isset($value->siteform_id)) {
                $SiteForm = new SiteFormModel();
                $SiteForm->find($value->siteform_id);
                $value->{'SiteForm'} = $SiteForm;
            }
        }

        return $collection;
    }
}
