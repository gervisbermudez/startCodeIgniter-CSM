<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacions extends MY_model {

	public $primaryKey = 'notificacion_id';
	public $softDelete = true;
	public function __construct()
	{
		parent::__construct();
	}

}