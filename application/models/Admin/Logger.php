<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Logger extends MY_Model
{

    public $primaryKey = 'logger_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {

            if (isset($value->type) && $value->type_id) {

                switch ($value->type) {
                    case 'pages':
                        $this->load->model('Admin/Page');
                        $page = new Page();
                        $result = $page->where(['page_id' => $value->type_id]);
                        $value->{'type_object'} = $result ? $result->first() : null;
                        $value->{'type_link'} = base_url('/admin/pages/editar/' . $value->type_id);
                        $value->{'type_description'} = $result ? $result->first()->title : null;
                        break;

                    default:
                        # code...
                        break;
                }

            }

            if (isset($value->user_id) && $value->user_id) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }
        return $collection;
    }

}
