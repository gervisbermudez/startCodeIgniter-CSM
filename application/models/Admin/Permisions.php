<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Permisions extends MY_model
{
    public $primaryKey = 'permisions_id';
    public $softDelete = true;
    public function __construct()
    {
        parent::__construct();
    }

}
