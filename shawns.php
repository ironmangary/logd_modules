
<?php
require_once("lib/http.php");

/*
Shaman Shawn's  (An alternative healer's hut for EarthGoneWrong)
by Gary M. Hartzell
Version 1.1 (09/16/20)
* Added support for the item: healing potion
* Adjusted to work in multiple cities

History:
v1.0 (05/24/20) - Initial release

*/

function shawns_getmoduleinfo(){
	$info = array(
		"name"=>"Shaman Shawn's",
		"version"=>"1.1",
		"author"=>"Gary Hartzell", 
		"category"=>"Village",
		"download"=>"",
		"settings"=>array(
		),
		"prefs"=>array(
		"Saved Variables,title",
		"employee"=>"Employee assisting user|rotting corpse",
		"cost"=>"Cost per hitpoint,int",
		"partial_heal"=>"Amount of hp user can afford to heal,int",
		));
	return $info;
	}

function shawns_install(){
	module_addhook("newday");
	module_addhook("village");
	module_addhook("forest");
	module_addhook("broakland_eastend");

	return true;
}

function shawns_uninstall(){
	return true;
}

function shawns_dohook($hookname, $args){
global $session;
switch($hookname){	
	case "newday":
		switch (e_rand(1,5)) {
			case 1:
				$employee="bored teenage elf";
				break;
			case 2:
				$employee="overly talkative fairy";
				break;
			case 3:
				$employee="retired old witch";
				break;
			case 4:
				$employee="rusty old robot";
				break;
			case 5:
				$employee="drooling half-ogre";
				break;
		}
		set_module_pref("employee", $employee);
		break;
	case "village":
		blocknav("healer.php");
		tlschema($args['schemas']['marketnav']);
		addnav($args['fightnav']);
		tlschema();
		addnav("Shaman Shawn's","runmodule.php?module=shawns");
	break;
	case "forest":
		blocknav("healer.php");
		addnav("Other");
		addnav("Shaman Shawn's","runmodule.php?module=shawns");
	break;
	case "broakland_eastend":
		addnav("East End");
		addnav("Shaman Shawn's","runmodule.php?module=shawns");
	break;
	}
	return $args;
}

function shawns_run() {

global $session;
$op = httpget('op');
page_header("Shaman Shawn's");
rawoutput("<center><h2>Shaman Shawn's</h2></center><br>");

$cost = round($session['user']['level'] * 0.75);
set_module_pref("cost", $cost);

switch ($op) {
	case "": case "main":
	$texts=array();
	output("`3You enter the tent of one of the many Shaman Shawn's found throughout The Lost Angeles area.  There are multiple lines of fighters waiting their turn to ");
	output("be healed by someone pretending to be a shaman.  There is a real person named Shawn who is really a shaman; and, the potions here do really ");
	output("work.  The employees who administer the potions, however, make only a small fraction of each healing potion they sell.  Capitalism at it's finest.  Oh yeah.`n`n");

$cost=get_module_pref("cost");
$employee=get_module_pref("employee");

	if ($session['user']['hitpoints'] < $session['user']['maxhitpoints']){
		$full_heal = ($session['user']['maxhitpoints'] - $session['user']['hitpoints']) * $cost;
		output("You finally reach the front of the line and you find yourself face-to-face with a %s.`n`n", $employee);
		output("\"Hi,\" you say awkwardly`n`nThe %s stares at you blankly.`n`n", $employee);
		output("\"So, how much will it cost me to, you know, get healed?\" you ask.`n`nThe %s reaches under the counter, producing a wand.  The ", $employee);
		output("%s places the wand on your forhead.  You feel an unsettling crawling sensation throughout your body which lasts around 10 seconds before a printer ", $employee);
		output("on the counter comes to life.  The %s takes out the printed page then looks at you.`n`n", $employee);
		output("\"A complete healing will cost you %s gold.\"", $full_heal);
		if ($session['user']['gold'] < $cost) {
			output("Counting your gold, you realize you don't have enough to even cure a hang nail.  Discouraged, you feel it's time to leave this place.  ");
			output("You mumble apologies to the %s and head towards the door, all too aware of everyone's eyes on you.", $employee);
		} elseif ($session['user']['gold'] < $full_heal) {
			$partial_heal = floor($session['user']['gold'] / $cost);
			set_module_pref("partial_heal", $partial_heal);

			output("Counting your gold, you realize you don't have enough for a complete healing.  You toss your purse on the counter and ask %s if they can do anything for you.`n`n", $employee);
			output("After counting your gold, the %s goes into a back room, coming out a minute later with a small vile of a bubbling liquid.  \"This is the best I can do,\" the %s explains.  ", $employee, $employee);
			output("\"This won't completely heal you, but should get you through...\"`n`n");
			output("\"Through until...?\" you ask the %s, who only shrugs in the way of a response.  What will you do?", $employee);
			addnav("Purchase partial healing", "runmodule.php?module=shawns&op=partialheal");
		} else {
			addnav("Purchase complete healing", "runmodule.php?module=shawns&op=fullheal");
		}
	}else {
		output("You finally reach the front of the line and you find yourself face-to-face with a %s.`n`n", $employee);
		output("\"What's your problem?\" the %s asks contemptously.  \"You don't need a healing potion.  Please leave now so we can attend to those who really need us.  Got it?\"`n`n", $employee);
		output("Everyone is staring at you now, and not very kindly.  Head down, you decide you better come back when you really need to be here.");
	}

	modulehook("shawns_main",$texts);
break;
case "fullheal":
$cost=get_module_pref("cost");
$employee=get_module_pref("employee");

$hp = $session['user']['maxhitpoints'] - $session['user']['hitpoints'];
$total_cost = $hp * $cost;

$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
$session['user']['gold'] -= $total_cost;

output("You pay the %s the %s gold and you are handed a vile of liquid you'd prefer not to think about.  You hold your nose and it's bottoms up`n`n", $employee, $total_cost);
output("You start to shake violently and your body feels like it is on fire.  The feeling passes as soon as it starts, though.  Once you get over the little episode");
output("you just endured, you get to your feet and realize that you feel much better.`n`nYou are completely healed!");
break;

case "partialheal":

$cost=get_module_pref("cost");
$employee=get_module_pref("employee");
$partial_heal=get_module_pref("partial_heal");

$total_cost = $partial_heal * $cost;

$session['user']['hitpoints'] += $partial_heal;
$session['user']['gold'] -= $total_cost;

output("You pay the %s the %s gold and you are handed a vile of liquid you'd prefer not to think about.  You hold your nose and it's bottoms up`n`n", $employee, $total_cost);
output("You start to shake violently and your body feels like it is on fire.  The feeling passes as soon as it starts, though.  Once you get over the little episode");
output("you just endured, you get to your feet and realize that you feel much better.`n`n");
output("You are healed %s health!", $partial_heal);
break;

}

//Page footer

	addnav("Leave");
	$userloc = $session['user']['location'];
	if ($userloc == "Broakland") { addnav("Return to Broakland","runmodule.php?module=broakland&op=eastend");}
		else { addnav("Return to Lost Angeles","village.php"); 
	}

if (is_module_active("item_healing_potion")) {
	$name = get_module_setting("name", "item_healing_potion");
	set_module_pref('where','runmodule.php?module=shawns','custom');
	addnav("Other");
	addnav(array("Purchase %s",$name),"runmodule.php?module=item_healing_potion&op=buy");
}

page_footer();
}
?>
