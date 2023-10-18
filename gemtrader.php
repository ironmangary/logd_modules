<?php

require_once("lib/http.php");

function gemtrader_getmoduleinfo(){
	$info = array(
		"name"=>"The Gem Trader",
		"version"=>"1.1",
		"author"=>"Gary Hartzell",
		"category"=>"Village Specials",
"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1015",
"settings"=>array(
	"gt_name"=>"What is the gem trader's name?,text|Don",
	"max_times"=>"How many times maximum will the gem trader visit?,range,1,5",
	"min_gold"=>"What is the minimum amount of gold the trader will offer for a gem?,int",
	"max_gold"=>"What is the maximum amount of gold the trader will offer for a gem?,int",
	"min_level"=>"What is the minimum level that a player can be visited?,range,1,15|1",
	"max_level"=>"What is the maximum level a player can be visited?,range,1,15|15"
),
"prefs"=>array(
	"times_visited"=>"How many times has been visited by the gem trader,int"
	)
);
return $info;
}

function gemtrader_install() {
	module_addeventhook("village", "return 100;");
	module_addhook("newday");
	return true;
}

function gemtrader_uninstall() {
	return true;
}

function gemtrader_dohook($hookname, $args){
	global $session;
	switch ($hookname) {
		case "newday":
		set_module_pref("times_visited", 0);
		break;
	}
	return $args;
}

function gemtrader_runevent($type, $link) {
	global $session;
	$gt_name = get_module_setting("gt_name");
	$op = httpget('op');
	switch ($op) {
	case "":
		$session['user']['specialinc'] = "module:gemtrader";
		if (((($session['user']['gems'] == 0)
			or (get_module_pref("times_visited") >= get_module_setting("max_times")))
			or ($session['user']['level'] < get_module_setting("min_level")))
			or ($session['user']['level'] > get_module_setting("max_level"))) {
				output("Something shiny catches the corner of your eye coming from a nearby alley.  You decide to investigate.`n`nYou find ");
				switch (e_rand(1,5)) {
				case 1:
					output("a gem!");
					$session['user']['gems'] ++;
					debuglog("Found a gem in the street.");
					break;
				case 2:
					output("a piece of tin foil.");
					break;
				case 3:
				case 4:
					output("a gold piece.  You wonder how you'll spend your fortune.");
					$session['user']['gold'] ++;
					debuglog("Found 1 gold in the street.");
					break;
				case 5:
					output("nothing.  It must have been a reflection.");
					break;
				}
						} else {
			set_module_pref("times_visited", get_module_pref("times_visited") + 1);
			$session['user']['specialmisc'] = e_rand(get_module_setting("min_gold"), get_module_setting("max_gold"));
			output("You come across a tall, well-dressed man.  He introduces himself ");
			output("as %s and extends a hand`n`n", $gt_name);
			output("\"I'm sure you are wondering what I want.\"  You nod and he continues \"I am ");
			output("looking for gems, and I'm willing to pay for them.\"`n`n");
			output("Thinking yourself a good negotiater, you proclaim, \"I may or may not have ");
			output("a gem.\"  \"If I did have a gem, how much would you be willing to pay?\"`n`n");
			output("%s rolls his eyes.  He then studies you for a moment, then seems to come ", $gt_name);
			output("to a decision.`n`n\"I'll give you %s gold for one ", $session['user']['specialmisc']);
			output("gem,\" he states, \"and that's my only offer.\"");
			addnav("Take the offer?");
			addnav("Yes", "village.php?op=yes");
			addnav("No", "village.php?op=no");
		}	
	break;
		
	case "yes":	
		output("You hand %s a gem, and he gives you the %s gold in return.`n`n", $gt_name, $session['user']['specialmisc']);
		output("He shakes your hand one last time and disappears into the crowd.");
		$session['user']['gold'] += $session['user']['specialmisc'];
		$session['user']['gems'] --;
		$session['user']['specialmisc'] = "";
		addnav("Continue","village.php");
	break;
	
	case "no":
		output("\"Suit yourself,\" %s says abruptly, and disappears into the crowd.", $gt_name);
		$session['user']['specialmisc'] = "";
		addnav("Continue","village.php");
	break;	
			}
			}	
			
function gemtrader_run() {
}
?>
