<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class SuscriptionsModel extends MY_Model {

	public $table = 'suscriptions';
	public $primaryKey = 'suscriptions_id';
	public $softDelete = true;

	function __construct()
	{
		parent::__construct();
	}
}