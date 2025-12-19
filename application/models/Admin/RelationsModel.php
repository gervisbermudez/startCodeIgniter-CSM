<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class RelationsModel extends MY_Model {
	public $table = 'relations';
	public $softDelete = true;

	function __construct()
	{
		parent::__construct();
	}

}
