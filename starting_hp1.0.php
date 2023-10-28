<?php

require_once("lib/http.php");

function starting_hp_getmoduleinfo(){
	$info = array(
		"name"=>"Starting HP",
		"version"=>"1.0",
		"author"=>"Gary Hartzell<br>Special Thanks to Dave S and Shadow Raven",
		"category"=>"Administrative",
"download"=>"http://www.legendarydragons.com/files/starting_hp1.0.zip",
"settings"=>array(
	"perdk"=>"What is the max amount of HP a player can start with (per dk)?,int|-1",
	"set to -1 for no limit,note",
	"overall"=>"What is the max amount of HP a player can start with regardless of dk?,int|-1",
	"set to -1 for no limit,note"
	),
);
return $info;
}

function starting_hp_install() {
	module_addhook("dragonkill");
	return true;
}

function starting_hp_uninstall() {
	return true;
}

function starting_hp_dohook($hookname, $args){
	global $session;
	switch ($hookname) {
	case "dragonkill":
		$perdk = get_module_setting("perdk");
		$overall = get_module_setting("overall");
		$cap = $perdk * $session['user']['dragonkills'];
		if ($perdk > 0 && $session['user']['maxhitpoints'] > $cap) {
			$session['user']['maxhitpoints'] = $cap;
			$session['user']['hitpoints'] = $cap;
		}
		if ($overall > 0 && $session['user']['maxhitpoints'] > $overall) {
			$session['user']['maxhitpoints'] = $overall;
			$session['user']['hitpoints'] = $overall;
		}
		$hpgain = array(
			'total' => $session['user']['maxhitpoints'],
			'dkpoints' => $dkpoints,
			'extra' => $session['user']['maxhitpoints'] - $dkpoints -($session['user']['level']*10),
			'base' => $dkpoints + ($session['user']['level'] * 10),
		);
		$hpgain = modulehook("hprecalc", $hpgain);
		$session['user']['maxhitpoints'] = 10 + $hpgain['dkpoints'] +$hpgain['extra'];
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		break;
	}
	return $args;
}

function starting_hp_run() {
}
?>
