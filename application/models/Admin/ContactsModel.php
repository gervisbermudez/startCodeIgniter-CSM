<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ContactsModel extends MY_Model {

	public $table = 'contacts';
	public $primaryKey = 'contacts_id';
    public $softDelete = true;

	public function __construct()
	{
		parent::__construct();
	}
}