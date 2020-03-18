<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Video extends MY_model {

	public $primaryKey = 'video_id';

	function __construct()
	{
		parent::__construct();
	}
}