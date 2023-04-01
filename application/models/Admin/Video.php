<?php

use Tightenco\Collect\Support\Collection;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Video extends MY_model
{

    public $primaryKey = 'video_id';
    public $softDelete = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_categoria($where = [])
    {
        return new Collection();
    }
}
