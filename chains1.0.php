<?php
require_once("lib/http.php");
require_once("lib/buffs.php");

/*
Chains from the Shades
Version 1.0 (02/26/06)
by Gary M. Hartzell
*/

function chains_getmoduleinfo(){
	$info = array (
		"name"=>"Chains from the Shades",
		"version"=>"1.0",
		"author"=>"Gary Hartzell",
		"category"=>"General",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=142",
		"settings"=>array(
			"chance"=>"Percentage chance that a player will be resurrected in chains,range,0,100,5|25",
			"roundsinchains"=>"How many rounds will the chains last?,int"
		),
	);
	return $info;
}

function chains_install(){
	module_addhook("newday");
	return true;
}

function chains_uninstall(){
	return true;
}

function chains_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "newday":
			if ($session['user']['spirits'] < -2) {
				$chance = get_module_setting("chance");
				$rounds = get_module_setting("roundsinchains");
				if (e_rand(1, 100) <= $chance) {
					output("`n`nYou arise from the Shades wrapped in chains!`n");
					$buff = array (
						"name"=>"Wrapped in Chains",
						"rounds"=>$rounds,
						"atkmod"=>0.7,
						"defmod"=>0.9,
						"roundmsg"=>"You have a difficult time fighting under the weight of the chains around you.",
						"wearoff"=>"You finally break free of those chains!"
					);
					apply_buff("chains", $buff);
				}
			}
		break;
	}
	return $args;
}

function chains_run() {
}
?>
