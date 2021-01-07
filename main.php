<?php
/* 1. Schema */
require_once __DIR__."/classes/DialPlan.class.php";

/* 2. Data */
require_once __DIR__."/data/data.php";

echo $dialPlan->printAsteriskScript();

