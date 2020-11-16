<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class Page_type extends MY_model
{
    public $primaryKey = 'page_type_id';
    public $softDelete = true;
    public function __construct()
    {
        parent::__construct();
    }

}
