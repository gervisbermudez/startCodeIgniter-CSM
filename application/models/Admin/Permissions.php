<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Permissions extends MY_Model
{

    public $table = "permisions";
    public $primaryKey = 'permisions_id';

    public function __construct()
    {
        parent::__construct();
    }

}
