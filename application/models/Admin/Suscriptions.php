<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Suscriptions extends MY_model {

	public $primaryKey = 'suscriptions_id';
	public $softDelete = true;

	function __construct()
	{
		parent::__construct();
	}
}