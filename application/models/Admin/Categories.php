<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Categories extends MY_model {
    
	public $primaryKey = 'categorie_id';

	public function __construct()
	{
		parent::__construct();
	}

	public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id) && $value->user_id) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }

        return $collection;
    }

}