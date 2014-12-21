<?php

$app = require_once("lib/bootstrap.php");
$collected = $app['db']->exec("DELETE FROM cached_players WHERE expires < NOW()");

echo($collected . " old UUID records expunged.");