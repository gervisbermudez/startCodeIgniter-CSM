<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class menu {

	var $ul = "<ul>";
	var $li = "<li>";
    var $char = '"';
	
    function __construct() {
    	
    }

    public function get_menu($arrayLinks = null)
    {
    	if ($arrayLinks == null) {
    		return null;
    	}

    	$menu = "";
    	$li = "";
    	$menu .= $this->ul;
    	$li .= $this->li;
        $char = $this->char;
    	foreach ($arrayLinks as $key => $value) {
    		$link = "<a ";
    		foreach ($value as $foo => $bar) {
    			$link .= $foo.'='.$char.$bar.$char.' ';
    		}
    		$link .= " > $key </a>";
    		$menu .= $li."$link"."</li>";
    	}
    	$menu .= "</ul>";
    	return $menu;
    }

    public function set_ul_properties($arrayProperties = array())
    {
    	$char = $this->char;
        $ul = "<ul ";
    	foreach ($arrayProperties as $key => $value) {
    		$ul .= $key.'='.$char.$value.$char.' ';
    	}
    	$ul .= "> ";
    	$this->ul = $ul;
    }

    public function set_li_properties($arrayProperties = array())
    {
    	$char = $this->char;
        $li = "<li ";
    	foreach ($arrayProperties as $key => $value) {
    		$li .= $key.'='.$char.$value.$char.' ';
    	}
    	$li .= "> ";
    	$this->li = $li;
    }

}

/* End of file Someclass.php */