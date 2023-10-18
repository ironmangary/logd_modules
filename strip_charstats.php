<?php

/*
Strip Charstats for Legend of the Green Dragon
by Gary Hartzell (10/18/2023)

This is a function and simple module to strip out charstats -- much like blocknav() does for navs.

*/

function stripcharstats_getmoduleinfo(){
	$info = array(
		"name"=>"Strip Charstats",
		"version"=>"1.0",
		"author"=>"Gary Hartzell", 
		"category"=>"Administrative",
		"download"=>"",
		);
	return $info;
	}

function stripcharstats_install(){
	module_addhook("charstats");
	return true;
}

function stripcharstats_uninstall(){
	return true;
}

function stripcharstats_dohook($hookname, $args){
	global $session;
	
	switch($hookname){	
		case "charstats":
			//Let's define the function first.
			
			function stripcharstat($value) {
				require_once("lib/pageparts.php");
				$newvalue = getcharstat_value($attribute);
				$newvalue = '';
				return $newvalue;
			}
			
			//Now, let's strip some stats.
			$html = stripcharstat("Constitution");
			$html .= stripcharstat("Spirits");
			output($html);
		break;
}
	return $args;
}

function stripcharstats_run() {
}
?>
