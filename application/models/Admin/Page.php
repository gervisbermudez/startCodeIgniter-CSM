<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class Page extends MY_model
{
    public $primaryKey = 'page_id';

    public function __construct()
    {
        parent::__construct();
    }
}
