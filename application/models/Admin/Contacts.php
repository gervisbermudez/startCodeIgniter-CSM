<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contacts extends MY_model {

	public $primaryKey = 'contacts_id';

	public function __construct()
	{
		parent::__construct();
	}
}