<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Relations extends MY_Model {
	public $softDelete = true;

	function __construct()
	{
		parent::__construct();
	}

}
