<?php
/* 1. Schema */
spl_autoload_register(function ($class) { require_once __DIR__.'/class/' . $class . '.class.php';});

/* 2. Data */
require_once __DIR__."/data/Gandarinha.php";

$mode = 0;
define("MODE_DIAL_PLAN_SCRIPT", 1);
define("MODE_PJSIP_CONF", 2);

function usage() {
	echo "{$_SERVER["argv"][0]} (--dialplan | --pjsip)";
	die(1);
}

foreach($argv as $arg) {
	if ($arg == "--dialplan") $mode = MODE_DIAL_PLAN_SCRIPT;
	if ($arg == "--pjsip")    $mode = MODE_PJSIP_CONF;
}

if ($mode == MODE_DIAL_PLAN_SCRIPT)
	echo $dialPlan->printAsteriskScript();
elseif($mode == MODE_PJSIP_CONF)
	echo "";
else
	usage();

