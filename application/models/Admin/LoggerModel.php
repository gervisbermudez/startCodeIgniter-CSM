<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class LoggerModel extends MY_Model
{
    public $table = 'logger';
    public $primaryKey = 'logger_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/UserModel');
        foreach ($collection as $key => &$value) {

            if (isset($value->type) && $value->type_id) {

                switch ($value->type) {
                    case 'pages':
                        $this->load->model('Admin/PageModel');
                        $page = new PageModel();
                        $result = $page->where(['page_id' => $value->type_id]);
                        $value->{'type_object'} = $result ? $result->first() : null;
                        $value->{'type_link'} = base_url('/admin/pages/editar/' . $value->type_id);
                        $value->{'type_description'} = $result ? $result->first()->title : null;
                        break;
                    
                    case 'config':
                        $this->load->model('Admin/SiteConfigModel');
                        $config = new SiteConfigModel();
                        $result = $config->where(['site_config_id' => $value->type_id]);
                        $value->{'type_object'} = $result ? $result->first() : null;
                        $value->{'type_link'} = base_url('/admin/configuracion/');
                        $value->{'type_description'} = $result ? ($result->first()->config_label ?: $result->first()->config_name) : null;
                        break;

                    default:
                        # code...
                        break;
                }

            }

            if (isset($value->user_id) && $value->user_id) {
                $user = new UserModel();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }
        return $collection;
    }

}
