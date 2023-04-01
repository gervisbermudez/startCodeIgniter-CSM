<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Siteform_submit extends MY_model
{
    public $primaryKey = 'siteform_submit_id';
    public $table = 'siteform_submit';
    public $hasOne = [
        'siteform' => ['siteform_id', 'Admin/SiteForm', 'SiteForm'],
        'user_tracking' => ['user_tracking_id', 'Admin/User_tracking', 'User_tracking'],
    ];
    public $hasData = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/SiteForm');
        foreach ($collection as $key => &$value) {
            if (isset($value->siteform_id)) {
                $SiteForm = new SiteForm();
                $SiteForm->find($value->siteform_id);
                $value->{'SiteForm'} = $SiteForm;
            }
        }

        return $collection;
    }
}
