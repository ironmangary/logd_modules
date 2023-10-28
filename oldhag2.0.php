<?php

function oldhag_getmoduleinfo(){
$info = array(
"name"=>"Old Hag",
"version"=>"2.0",
"author"=>"Gary Hartzell", 
"category"=>"Forest Specials",
"download"=>"http://dragonprime.net/users/Ironman/oldhag2.zip",
);
return $info;
}

function oldhag_install(){
module_addeventhook("forest",
"return (is_module_active('cities')?0:100);");
return true;
}

function oldhag_uninstall(){
return true;
}

function oldhag_dohook($hookname,$args){
return $args;
}

function oldhag_runevent($type,$link) {
global $session;
$maxhp = $session['user']['maxhitpoints'];
$from = $link;
$op = httpget('op');
$session['user']['specialinc'] = "module:oldhag";
if ($op==""){
output("`2As you are wandering through the forest, an ugly old hag"); 
output("appears before you.`n`n\"Give me a gem, and I'll make ye"); 
output("feel better,\" she screeches.`n`n");
output("Do you give her a gem?`0");
addnav("Give her a gem",$from."op=yes");
addnav("Don't do it",$from."op=no");
} elseif ($op=="no") {
output("\"Humph,\" replies the hag.  She waves her cane before her and vanishes.");
$session['user']['specialinc'] = "";
} else {
if ($session['user']['gems'] < 1)
{
output("\"Ye have no gems, ye lyin' fool!\" the hag screams.  She strikes you between the eyes with her cane and vanishes.");
$session['user']['hitpoints'] = 1;
}
else
{
$session[user][gems] --;
if ($session['user']['hitpoints'] < $maxhp)
{
output("She waves her cane in front of your face, and you feel a surge of energy go through your body`n`nYOU ARE COMPLETELY HEALED!");
$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
}
else
{
output("She waves her cane in front of your face, and you feel a surge of energy go through your body`n`nYOU GAIN A MAX HIT POINT!");
$session[user][maxhitpoints]++;
$session[user][hitpoints]++;
}
}
}
}


function oldhag_run(){
}

?>