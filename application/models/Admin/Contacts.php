<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Contacts extends MY_Model {

	public $primaryKey = 'contacts_id';
    public $softDelete = true;

	public function __construct()
	{
		parent::__construct();
	}
}