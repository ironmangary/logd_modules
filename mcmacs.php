
<?php
require_once("lib/http.php");

/*
McMacs (for EarthGoneWrong)
by Gary M. Hartzell
Version 1.2.1 (12/12/22)
* Updated to work with new travel system, other minor tweaks.

Requires the stamina module.


History:
v1.2 (09/16/20) - Adjusted to work in multiple cities
v1.1 (06/05/20) - Updated to work with new stamina system.
v1.0 (04/01/14) - Initial release

*/

function mcmacs_getmoduleinfo(){
	$info = array(
		"name"=>"McMac's",
		"version"=>"1.2.1",
		"author"=>"Gary Hartzell", 
		"category"=>"Village",
		"download"=>"",
		"settings"=>array(
		),
		);
	return $info;
	}

function mcmacs_install(){
	module_addhook("village");
	return true;
}

function mcmacs_uninstall(){
	return true;
}

function mcmacs_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "village":
			tlschema($args['schemas']['tavernnav']);
			addnav($args['tavernnav']);
			tlschema();
			addnav("McMacs","runmodule.php?module=mcmacs");
	break;	
	return $args;
}

function mcmacs_run() {

	global $session;
$op = httpget('op');
page_header("McMac's");
rawoutput("<center><h2>McMac's</h2></center><br><br>");

switch ($op)
{

//Main Menu 

	case "":
	case "main":
		$max_fullness = get_module_setting('max_fullness', 'stamina');
		$user_fullness = get_module_pref('fullness', 'stamina');
		output("`nA couple centuries ago, a once dominant name in fast food and a once dominant name in technology merged.  The result?  McMac's.`n");
		if ($user_fullness >= $max_fullness) {
			output("`nAs you walk through the doors, the smell of the fast food doesn't entice you as it normally would.  You are just too full from your earlier meals.  You decide to leave before you are physically ill.");
			} else {
			output("`nAs you walk through the doors, the smell of the greasy goodness hits you, and you walk up to the counter to place your order while a bored teenage employee stares at you impatiently.");
			addnav("Meals");
			addnav("iHappyMeal w/A burger, apple slices, and a iSoda - 300 gold","runmodule.php?module=mcmacs&op=ihappy");
			addnav("iHappyMeal w/a chicken sandwich, apple slices, and a iSoda - 300 gold","runmodule.php?module=mcmacs&op=ihappy");
			addnav("iHappyMeal w/a fish sandwich, apple slices, and a iSoda - 300 gold","runmodule.php?module=mcmacs&op=ihappy");
			addnav("McAppleSalad - 200 gold","runmodule.php?module=mcmacs&op=salad");
			addnav("iMcBreakfastBuffet, All Day! - 440 gold","runmodule.php?module=mcmacs&op=breakfast");
			addnav("Beverages");
			addnav("iSoda - 50 gold","runmodule.php?module=mcmacs&op=soda");
			addnav("McAppleJuice - 70 gold","runmodule.php?module=mcmacs&op=juice");
			addnav("Dessert");
			addnav("McAppleMilkShake - 140 gold","runmodule.php?module=mcmacs&op=shake");
			addnav("McApplePie - 110 gold","runmodule.php?module=mcmacs&op=pie");
			}
			break;

	case "ihappy":
		if ($session['user']['gold'] < 300) {
			output("`nYou are about to order the iHappyMeal, but you realize that you don't have enough gold with you.  The teenager behind the counter is not impressed and rolls her eyes.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou order the iHappyMeal, and give the teenager your 300 gold.  Not aminute later, she hands you your food.  You find a place to sit and enjoy the greasy but delicious meal and you now know why it's called an iHappyMeal.");
			output("`n`nYou gain stamina!");
			$session['user']['gold'] -= 300;
			increment_module_pref('stamina', +4, 'stamina');
			$user_fullness += 5;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	case "salad":
		if ($session['user']['gold'] < 200) {
			output("`nYou are about to order the McAppleSalad, but you realize that you don't have enough gold with you.  The teenager behind the counter is not impressed and rolls her eyes.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou order the McAppleSalad, a light and healthy alternative to the greasy iHappyMeal.  You give the teenager your 200 gold.  Not even a minute later, she hands you your food.  You find a place to sit and enjoy the salad.");
			output("`n`nYou gain stamina!");
			$session['user']['gold'] -= 200;
			increment_module_pref('stamina', +3, 'stamina');
			$user_fullness += 3;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	case "breakfast":
		if ($session['user']['gold'] < 440) {
			output("`nYou are about to order the iMcBreakfastBuffet, but you realize that you don't have enough gold with you.  The teenager behind the counter is not impressed and rolls her eyes.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou order the iMcBreakfastBuffet and hand the girl 440 gold.  You immediately take to the breakfast bar, where you are overwhelmed by all the options.  Breakfast is the most important meal of the day, after all.");
			output("`n`nYou gain stamina!");
			$session['user']['gold'] -= 440;
			increment_module_pref('stamina', +5, 'stamina');
			$user_fullness += 8;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	case "soda":
		if ($session['user']['gold'] < 50) {
			output("`nYou are about to order the iSoda, but you realize that you don't have enough gold with you.  The teenager behind the counter sighs.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou decide you aren't that hungry, but you could use a sugary drink.  You order the iSoda and hand the girl 50 gold before downing the beverage.");
			output("`n`nYou gain some stamina!");
			$session['user']['gold'] -= 50;
			increment_module_pref('stamina', +1, 'stamina');
			$user_fullness += 2;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	case "juice":
		if ($session['user']['gold'] < 70) {
			output("`nYou are about to order the McAppleJuice, but you realize that you don't have enough gold with you.  The teenager behind the counter sighs impatiently.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou decide you aren't that hungry, but you are a bit thirsty.  Going for a healthier option, you order the McAppleJuice and hand the girl 70 gold.");
			output("`n`nYou gain stamina!");
			$session['user']['gold'] -= 70;
			increment_module_pref('stamina', +2, 'stamina');
			$user_fullness += 1;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	case "shake":
		if ($session['user']['gold'] < 140) {
			output("`nYou are about to order the McAppleMilkShake, but you realize that you don't have enough gold with you.  The teenager behind the counter is not impressed and rolls her eyes.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou order the McAppleMilkShake, and give the teenager your 140 gold.  Moments later, you are sucking down a delicious milk shake.");
			output("`n`nYou gain stamina!");
			$session['user']['gold'] -= 140;
			increment_module_pref('stamina', +3, 'stamina');
			$user_fullness += 3;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	case "pie":
		if ($session['user']['gold'] < 110) {
			output("`nYou are about to order the McApplePie, but you realize that you don't have enough gold with you.  The teenager behind the counter is not impressed and rolls her eyes.");
			addnav("Try again","runmodule.php?module=mcmacs&op=main");
			} else {
			output("`nYou order the McApplePie, and give the teenager your 110 gold.  She then hands you the little push up packaging that they have used for millennia and you eagerly devour the sweet treat.");
			output("`n`nYou gain stamina!");
			$session['user']['gold'] -= 110;
			increment_module_pref('stamina', +3, 'stamina');
			$user_fullness += 4;
			set_module_pref('fullness', $user_fullness, 'stamina');
			}
		break;

	}
		addnav("Leave");
		require_once("lib/villagenav.php");
		villagenav();
		page_footer();
	}
?>
