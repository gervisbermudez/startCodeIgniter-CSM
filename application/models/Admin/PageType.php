<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class PageType extends MY_Model
{
    public $table = 'page_type';
    public $primaryKey = 'page_type_id';
    public $softDelete = true;
    public function __construct()
    {
        parent::__construct();
    }

}
