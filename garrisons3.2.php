<?php
require_once("lib/http.php");
require_once("lib/villagenav.php");

/*
Garrison's Training Center
Version 3.2 (04/14/07)
by Gary M. Hartzell
*/

function garrisons_getmoduleinfo(){
	$info = array(
		"name"=>"Garrison's Training Center",
		"version"=>"3.2",
		"author"=>"Gary Hartzell", 
		"category"=>"Village",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=285",
		"settings"=>array(
		"Garrison's Training Center - General,title",
		"gtcloc"=>"Where does the Center appear,location|".getsetting("villagename", LOCATION_FIELDS),
		"min_dk"=>"How many dk's are needed before the Center is available?,int|0",
		"max_dk"=>"After how many dk's is the Center no longer available (0=infinite)?,int|0",
		"Garrison's Training Center - Bruno,title",
		"m_curr"=>"Does Bruno charge in gold or gems?,enum,0,Gold,1,Gems|0",
		"m_cost"=>"How much does a massage cost?,int|200",
		"Garrison's Training Center - Court,title",
"c_lessons"=>"How many total lessons can a player have with Court before a dragon kill (0=unlimited)?,int|0",
		"c_turns"=>"How many turns are lost by training with Court?,int|0",
		"c_curr"=>"Does Court charge in gold or gems?,enum,0,Gold,1,Gems,2,Neither|2",
		"c_cost"=>"How much does Court charge (Setting the previous value to \"Neither\" negates this)?,int",
		"Garrison's Training Center - Garrison,title",
		"g_lessons"=>"How many total lessons can a player have with Garrison before a dragon kill?,int|0",
		"g_level"=>"At what level does Garrison become available?,range,10,15,1",
		"g_turns"=>"How many turns are lost by training with Garrison?,int|0",
		"g_curr"=>"Does Garrison charge in gold or gems?,enum,0,Gold,1,Gems,2,Neither|2",
		"g_base_cost"=>"How much does Garrison charge (setting the previous setting to \"Neither\" negates this)?,int",
		"g_dk_cost"=>"How much additional per DK does Garrison charge?,int",
		"g_max_cost"=>"What is the absolute maximum Garrison will charge (0 = no maximum)?,int"
						),
		"prefs"=>array(
		"Garrison's Training Center User Preferences,title",
		"seencourt"=>"Seen Court today?,bool|0",
		"seengarrison"=>"Seen Garrison today?,bool|0",
		"seenbruno"=>"Seen Bruno today?,bool|0",
		"c_usedlessons"=>"Lessons with Court?,int",
		"g_usedlessons"=>"Lessons with Garrison?,int",
		"g_cost"=>"Cost to train with Garrison?,int"
		),
		);
	return $info;
	}

function garrisons_install(){
	module_addhook("village");
	module_addhook("newday");
	module_addhook("dragonkill");
	return true;
}

function garrisons_uninstall(){
	return true;
}

function garrisons_dohook($hookname, $args){
	global $session;
	
switch($hookname){	
	case "newday":
		set_module_pref("seencourt",0);
		set_module_pref("seengarrison",0);
		set_module_pref("seenbruno",0);
				break;

	case "village":
		$max_dk = get_module_setting("max_dk");
		$min_dk = get_module_setting("min_dk");
		$dk = $session['user']['dragonkills'];
		if ($dk >= $min_dk && (($max_dk !=0 && $dk <= $max_dk) || $max_dk == 0)) { 
			if ($session['user']['location'] == get_module_setting("gtcloc")) 
				{
					addnav($args['fightnav']);
					tlschema();
					addnav("Garrison's Training Center","runmodule.php?module=garrisons");
				}
		}
		break;
		
	case "dragonkill":
		set_module_pref("c_usedlessons",0);
		set_module_pref("g_usedlessons",0);
	break;
	}
	return $args;
}

function garrisons_run() {

	global $session;
$op = httpget('op');
page_header("Garrison's Training Center");

switch ($op)
{

//Main Menu 

	case "":
	case "lobby":
		$g_level = get_module_setting("g_level");
		$c_lessons = get_module_setting("c_lessons");
		$g_lessons = get_module_setting("g_lessons");
		$c_ul = get_module_pref ("c_usedlessons");
		$g_ul = get_module_pref("g_usedlessons");
		$g_rl = $g_lessons - $g_ul;
		$c_rl = $c_lessons - $c_ul;
		output("`^You enter The Training Center filled with anticipation and a little fear.  Cries of both triumph and pain echo out of the various rooms.  You take a deep breath, and prepare yourself for one hell of a workout.`n`n");
		if ($c_lessons >0) output("`n`nYou have %s lessons remaining with Court.",$c_rl);
		if ($g_lessons > 0 && $session['user']['level'] >= $g_level) output("`nYou have %s lessons remaining with Garrison",$g_rl);
		addnav("C?Train with Court","runmodule.php?module=garrisons&op=court");
		if ($session[user][level] >= $g_level)
		{
			addnav("G?Train with Garrison","runmodule.php?module=garrisons&op=garrison");
		}
		addnav("M?Get a Massage","runmodule.php?module=garrisons&op=massage");
		break;

//Court

	case "court":
		$seencourt = get_module_pref("seencourt");
		$c_ul = get_module_pref("c_usedlessons");
		$c_lessons = get_module_setting("c_lessons");
		$c_curr = get_module_setting("c_curr");
		$c_cost = get_module_setting("c_cost");
		$c_turns = get_module_setting("c_turns");
		if ($c_lessons > 0 && $c_ul >= $c_lessons)
		{
			output("You have finished your training with Court.");
			addnav("L?Return to the lobby","runmodule.php?module=garrisons&op=lobby");
			} else if ($seencourt != 0)
		{
			output("Court doesn't have any more time for you today.");
			addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		}
		else if ($c_turns > 0 && $session['user']['turns'] < $c_turns)
		{
			output("You are too tired to go through any training right now.");
			addnav("L?Return to the lobby","runmodule.php?module=garrisons&op=lobby");
		} else {
			output("Court looks annoyed as you enter his gym.`n`n");
			if ($c_curr ==2)
			{
				set_module_pref("seencourt",1);
				$c_ul ++;
				set_module_pref("c_usedlessons",$c_ul);
				output("\"What do you want, maggot?  I'm a busy guy and I only have time to give you one lesson, so you better think fast.\"");
				addnav("S?Strength","runmodule.php?module=garrisons&op=cs");
				addnav("D?Defense","runmodule.php?module=garrisons&op=cd");
				addnav("E?Endurance","runmodule.php?module=garrisons&op=ch");
				addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
			} else {
				if ($c_curr == 0)
				{
					$c_currname = "gold";
				} else {
					$c_currname = "gems";
				}
				output("\"My services aren't free, you know.  This is gonna cost you %s %s.  Well?\"",$c_cost,$c_currname);
				addnav("Pay for a lesson","runmodule.php?module=garrisons&op=c_pay");
				addnav("Never mind","runmodule.php?module=garrisons&op=lobby");
			}
		}
		break;

			case "c_pay":
				$c_ul = get_module_pref("c_usedlessons");
				$c_curr = get_module_setting("c_curr");
				$c_cost = get_module_setting("c_cost");
				if ($c_curr == 0 && $session['user']['gold'] < $c_cost)
				{
					output("You try to come up with %s gold, but can't.  Court starts ",$c_cost);
					output("yelling obsenities and before you know it, he hits you hard, sending ");
					output("you to the floor.  You feel a trip to the Healer's Hut is in order.");
					$session['user']['hitpoints'] = 1;
				} else if ($c_curr == 1 && $session['user']['gems'] < $c_cost)
				{
					output("You try to come up with %s gold, but can't.  Court starts ",$c_cost);
					output("yelling obsenities and before you know it, he hits you hard, sending you to ");
					output("the floor.  You feel a trip to the Healer's Hut is in order.");
					$session['user']['hitpoints'] = 1;
				} else {
					if ($c_curr == 0) $session['user']['gold'] -= $c_cost;
					if ($c_curr ==1) $session['user']['gems'] -= $c_cost;
					set_module_pref("seencourt",1);
					$c_ul ++;
					set_module_pref("c_usedlessons",$c_ul);
					output("\"So, what do you want, maggot?\"");
					addnav("Strength","runmodule.php?module=garrisons&op=cs");
					addnav("Defense","runmodule.php?module=garrisons&op=cd");
					addnav("Endurance","runmodule.php?module=garrisons&op=ch");
				}
				break;
				
	case "cs":
		$c_turns = get_module_setting("c_turns");
		output("After a grueling workout with Court, you actually feel stronger.`n`n");
		output("YOU GAIN 1 STRENGTH!");
		$session['user']['attack']+=1;
		debuglog("Gained 1 attack from Court.");
		if ($c_turns > 0)
		{
			output("`n`nThe session has left you tired, and you don't think you will be able to adventure as much today.");
			$session['user']['turns'] -= $c_turns;
		}
		addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		break;
		

	case "cd":
		$c_turns = get_module_setting("c_turns");
		output("After a challenging session with Court, you feel better equipped to defend yourself.`n`n");
		output("YOU GAIN 1 DEFENSE!");
		debuglog("Gained 1 defense from Court.");
		$session['user']['defense'] ++;
		if ($c_turns > 0)
		{
			output("`n`nThe session has left you tired, and you don't think you will be able to adventure as much today.");
			$session['user']['turns'] -= $c_turns;
		}
		addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		break;
		
	case "ch":
		$c_turns = get_module_setting("c_turns");
		output("After an intense hour, you feel tougher.`n`n");
		output("YOU GAIN 1 HIT POINT!");
		debuglog("Gained 1 max hit point from Court.");
		$session['user']['maxhitpoints']+=1;
		if ($c_turns > 0)
		{		
			output("`n`nThe session has left you tired, and you don't think you will be able to adventure as much today.");
			$session['user']['turns'] -= $c_turns;
		}
		addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		break;
	
//Garrison

	case "garrison":
		$seengarrison = get_module_pref("seengarrison");
		$g_lessons = get_module_setting("g_lessons");
		$g_ul = get_module_pref("g_usedlessons");
		$g_curr = get_module_setting("g_curr");
		$g_turns = get_module_setting("g_turns");
		if ($g_lessons > 0 && $g_ul >= $g_lessons)
		{
			output("Unfortunately, you don't have any lessons remaining with Garrison.");
		} else if ($seengarrison != 0)
		{
			output("Garrison has many other warriors to train yet today.  Try back tomorrow.");
			addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
					}
		else if ($g_turns > 0 && $session['user']['turns'] < $g_turns)
		{
			output("You are too tired to go through training with Garrison right now.");
		} else {
			output("You enter Garrison's gym and wait while the great man spars with another muscular warrior.  It turns out, you don't have to wait very long.  Garrison approaches you.  He looks like a statue up close, and you notice he hasn't even broken a sweat from all his training and sparring sessions of the day.`n`n");
			if ($g_curr == 2)
			{
				set_module_pref("seengarrison",1);
				$g_ul ++;
				set_module_pref("g_usedlessons",$g_ul);
				output("\"So, you need a lesson,\" he says to you.  \"You are, indeed, an advanced warrior, but we all can use some help from time to time.  So, what do you feel you need the most help with?\"");
				addnav("S?Strength","runmodule.php?module=garrisons&op=gs");
				addnav("D?Defense","runmodule.php?module=garrisons&op=gd");
				addnav("E?Endurance","runmodule.php?module=garrisons&op=gh");
				addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
			} else {
				if ($g_curr == 0)
				{
					$g_currname = "gold";
				} else {
					$g_currname = "gems";
				}
				$g_max = get_module_setting("g_max_cost");
				$g_total = get_module_setting("g_base_cost") + (get_module_setting("g_dk_cost") * $session['user']['dragonkills']);
				if ($g_max != 0 && $g_total > $g_max) {
					$g_cost = $g_max;
				} else {
					$g_cost = $g_total;
				}
				set_module_pref("g_cost", $g_cost);
				output("\"So, you need a lesson,\" he says to you.  \"You are, indeed, ");
				output("an advanced warrior, but we all can use some help from time to time.  ");
				output("First thing's first.  My fee is %s %s.\"",$g_cost,$g_currname);
				addnav("Pay for a lesson","runmodule.php?module=garrisons&op=g_pay");
				addnav("Never mind","runmodule.php?module=garrisons&op=lobby");
			}
		}
		break;

	case "g_pay":
		$g_ul = get_module_pref("g_usedlessons");
		$g_curr = get_module_setting("g_curr");
		$g_cost = get_module_pref("g_cost");
		if ($g_curr == 0)
		{
			$g_currname = "gold";
		} else {
			$g_currname = "gems";
		}
		if ($g_curr == 0 && $session['user']['gold'] < $g_cost)
		{
			output("You rummage through your possessions, but can't seem to come up with %s gold.  Garrison frowns.  \"Get out of my center before I throw you out,\" he demands.  You think it's wise to do as he says.",$g_cost, $g_currname);
		} else if ($g_curr == 1 && $session['user']['gems'] < $g_cost)
		{
			output("You rummage through your possessions, but can't seem to come up with %s gems.  Garrison frowns.  \"Get out of my center before I throw you out,\" he demands.  You think it's wise to do as he says.",$g_cost);
		} else
		{
			if ($g_curr == 0) $session['user']['gold'] -= $g_cost;
			if ($g_curr == 1) $session['user']['gems'] -= $g_cost;
			set_module_pref("seengarrison",1);
			$g_ul ++;
			set_module_pref("g_usedlessons",$g_ul);
			output("\"Now then.  What do you feel you need the most help with?\" Garrison asks you.");
			addnav("Strength","runmodule.php?module=garrisons&op=gs");
			addnav("Defense","runmodule.php?module=garrisons&op=gd");
			addnav("Endurance","runmodule.php?module=garrisons&op=gh");
		}
			break;
			
	case "gs":
		$g_turns = get_module_setting("g_turns");
		output("You train hard with The Great Garrison for a half-hour.`n`n");
		switch(e_rand(1,4))
		{
			case 1:
				output("\"You surely need more training,\" Garrison comments as you lay panting on the mats.`n`n");
				output("YOU GAIN 1 STRENGTH.");
				debuglog("Gained 1 strength from Garrison.");
				$session['user']['attack']+=1;
				break;
			case 2:
				output("\"Excellent,\" Garrison remarks, extending a hand to help you off the mats.`n`n");
				output("YOU GAIN 3 STRENGTH!");
				debuglog("Gained 3 strength from Garrison.");
				$session['user']['attack']+=3;
				break;
			case 3:
				output("\"Not bad,\" Garrison says, leaving you in a puddle of your own sweat on the mats.`n`n");
				output("YOU GAIN 1 STRENGTH.");
				debuglog("Gained 1 strength from Garrison.");
				$session['user']['attack']+=1;
				break;
			case 4:
				output("\"Good show,\" Garrison says afterwards.  You can't help but to smile at the compliment.`n`n");
				output("YOU GAIN 2 STRENGTH.");
				debuglog("Gained 2 strength from Garrison.");
				$session['user']['attack']+=2;
				break;
		}
		if ($g_turns > 0)
		{
			output("`n`nThe session has left you tired, and you don't feel you'll be able to adventure as much today.");
			$session['user']['turns'] -= $g_turns;
		}
		addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		break;

	case "gd":
		$g_turns = get_module_setting("g_turns");
		output("You train hard with The Great Garrison for a half-hour.`n`n");
		switch(e_rand(1,4))
		{
			case 1:
				output("\"You surely need more training,\" Garrison comments as you lay panting on the mats.`n`n");
				break;
			case 2:
				output("\"Excellent,\" Garrison remarks, extending a hand to help you off the mats.`n`n");
				output("YOU GAIN 3 DEFENSE!");
				debuglog("Gained 3 defense from Garrison.");
				$session['user']['defense']+=3;
				break;
			case 3:
				output("\"Not bad,\" Garrison says, leaving you in a puddle of your own sweat on the mats.`n`n");
				output("YOU GAIN 1 DEFENSE.");
				debuglog("Gained 1 defense from Garrison.");
				$session['user']['defense']+=1;
				break;
			case 4:
				output("\"Good show,\" Garrison says afterwards.  You can't help but to smile at the compliment.`n`n");
				output("YOU GAIN 2 DEFENSE.");
				debuglog("Gained 2 defense from Garrison.");
				$session['user']['defense']+=2;
				break;
		}
		if ($g_turns > 0)
		{
			output("`n`nThe session has left you tired, and you don't feel you'll be able to adventure as much today.");
			$session['user']['turns'] -= $g_turns;
		}
		addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		break;
		
	case "gh":
		$g_turns = get_module_setting("g_turns");
		output("You train hard with The Great Garrison for a half-hour.`n`n");
		switch(e_rand(1,4))
		{
			case 1:
				output("\"You surely need more training,\" Garrison comments as you lay panting on the mats.`n`n");
				output("YOU GAIN 1 HIT POINT.");
				debuglog("Gained 1 max hit point from Garrison.");
				$session['user']['maxhitpoints']+=1;
				break;
			case 2:
				output("\"Excellent,\" Garrison remarks, extending a hand to help you off the mats.`n`n");
				output("YOU GAIN 3 HIT POINTS!");
				debuglog("Gained 3 max hit points from Garrison.");
				$session['user']['maxhitpoints']+=3;
				break;
			case 3:
				output("\"Not bad,\" Garrison says, leaving you in a puddle of your own sweat on the mats.`n`n");
				output("YOU GAIN 1 HIT POINT.");
				debuglog("Gained 1 max hit point from Garrison.");
				$session['user']['maxhitpoints']+=1;
				break;
			case 4:
				output("\"Good show,\" Garrison says afterwards.  You can't help but to smile at the compliment.`n`n");
				output("YOU GAIN 2 HIT POINTS!");
				debuglog("Gained 2 max hit points from Garrison.");
				$session['user']['maxhitpoints']+=2;
				break;
		}
if ($g_turns > 0)
		{
			output("`n`nThe session has left you tired, and you don't feel you'll be able to adventure as much today.");
			$session['user']['turns'] -= $g_turns;
		}
		addnav("Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		break;

//Massage

	case "massage":
		$seenbruno = get_module_pref("seenbruno");
		$m_cost = get_module_setting("m_cost");
		$m_curr = get_module_setting("m_curr");
		if ($m_curr == 0) {
			$m_currname = "gold";
		} else {
			$m_currname = "gems";
		}
		if ($seenbruno !=0)
		{
			output("The earliest Bruno can fit you in for a massage is tomorrow.");
			addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		}
					else
		{
			output("A very tall and muscular bald man grasps your hand and ");
			output("introduces himself as Bruno.  \"So, are you ready for a ");
			output("massage?\" he asks.  \"It will only cost you %s %s.\"", $m_cost, $m_currname);
			addnav("Y?Yes","runmodule.php?module=garrisons&op=massage2");
			addnav("N?No thanks","runmodule.php?module=garrisons&op=lobby");
		}
		break;

	case "massage2":
		$m_cost = get_module_setting("m_cost");
		$m_curr = get_module_setting("m_curr");
		if ($m_curr == 0) {
			$m_currname = "gold";
		} else {
			$m_currname = "gems";
		}
		if ($m_curr == 0 && $session['user']['gold'] < $m_cost) { 
			output("You rummage through your possessions but can't seem to find %s gold.  Maybe another time.", $m_cost);
			addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		} else if ($m_curr ==1 && $session['user']['gems'] < $m_cost) {
			output("You rummage through your possessions but can't seem to find %s gems.  Maybe another time.", $m_cost);
			addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		} else {
			set_module_pref("seenbruno",1);
			output("Bruno has an amazingly gentle touch for such a large man.  After only 20 minutes, you feel great.`n`n");
			if ($m_curr ==0) {
				$session['user']['gold'] -= $m_cost;
			} else {
				$session['user']['gems'] -= $m_cost;
			}
			switch(e_rand(1,6))
			{
				case 1:
				case 5:
					if ($session['user']['hitpoints'] < $session['user']['maxhitpoints']) {
						output("You feel so much better!  (You are completely healed.)");
						debuglog("Completely healed from Bruno.");
						$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
					} else {
						output("You feel extra healthy!");
						$session['user']['hitpoints'] += floor($session['user']['hitpoints'] * .25); 
						debuglog("Gained temp hp from Bruno.");
					}
					break; 
				case 2:
					output("You feel better about yourself!  (You gain 1 charm.)");
					debuglog("Gained 1 charm from Bruno.");
					$session['user']['charm'] += 1;
					break;
				case 3:
				case 6:
					output("You feel ready to take on the world!  (You gain 2 turns.)");
					debuglog("Gained 2 turns from Bruno.");
					$session['user']['turns'] += 2;
					break;
				case 4:
					output("You feel ready to take on the world!  (You gain 1 player fight.)");
					debuglog("Gained 1 player fight from Bruno.");
					$session['user']['playerfights']++;
					break;
			}
			addnav("L?Return to the Lobby","runmodule.php?module=garrisons&op=lobby");
		}
				}
	villagenav();
	page_footer();
}
?>
