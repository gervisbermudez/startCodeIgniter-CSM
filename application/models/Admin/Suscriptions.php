<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Suscriptions extends MY_Model {

	public $primaryKey = 'suscriptions_id';
	public $softDelete = true;

	function __construct()
	{
		parent::__construct();
	}
}