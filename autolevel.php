<?php
require_once("lib/http.php");
require_once("lib/experience.php");

/*
EarthGoneWrong.com Autolevel 
by Gary Hartzell
v1.01 

Version History

1.01 (10/03/20)
 - Oops!  I was never checking if max level was ever obtained.  Fixed.

1.0 (5/17/14)
 - Initial release

*/

function autolevel_getmoduleinfo(){
	$info = array(
		"name"=>"Autolevel",
		"version"=>"1.01",
		"author"=>"Gary Hartzell", 
		"category"=>"EGW Customizations",
		"download"=>"",
		);
	return $info;
	}

function autolevel_install(){
	module_addhook("battle-victory");
	return true;
}

function autolevel_uninstall(){
	return true;
}

function autolevel_dohook($hookname, $args){
	global $session;
	
switch($hookname){	
	case "battle-victory":
		$level = $session['user']['level'];
		$dks = $session['user']['dragonkills'];
		$exp = exp_for_next_level($level, $dks);
//		debug("Exp needed: ");
//		debug($exp);
		if ($session['user']['level'] < 15 && $session['user']['experience'] > $exp) {
			$session['user']['level']++;
			$session['user']['maxhitpoints']+=10;
			$session['user']['attack']++;
			$session['user']['defense']++;
			if ($session['user']['hitpoints'] < $session['user']['maxhitpoints']) $session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			output("`n`n`c*** YOU HAVE LEVELED UP! ***`c");
			output("`n`nYou gain 1 attack!");
			output("`nYou gain 1 defense!");
			output("`nYou gain 10 max hit points!");
			output("`n`n");
			modulehook("autolevel");
			output("`n`n");
			}
	break;
}
	return $args;
}

function autolevel_run() {
}
?>
