<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Categories extends MY_model {
    
	public $primaryKey = 'categorie_id';

	public function __construct()
	{
		parent::__construct();
	}

}