<?php 
	if (! function_exists('fnGetTitle'))
	{
		function fnGetTitle($strUrlSegment)
		{
			$porciones = explode("/", $strUrlSegment);
			array_key_exists(1, $porciones) ? $title = ucwords($porciones[0]." | ".$porciones[1]) : $title = ucwords($porciones[0]);
			array_key_exists(2, $porciones) ? $title = ucwords($porciones[0]." | ".$porciones[1]." - ".$porciones[2]) : false ;
			return $title;
		}
	}
?>