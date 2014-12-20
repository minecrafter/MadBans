<?php

/**
 * MadBans: Flexible web administration interface for many Minecraft banning plugins.
 *
 * Copyright (C) 2014 Tux. Licensed under MIT license.
 */

// Require F3.

// F3 doesn't work very well with Composer and the author kindly asks people who use Composer's
// autoloader to take a hike.

// Instead we'll include F3 here.
$f3 = require_once("vendor/bcosca/fatfree/lib/base.php");

// Require Composer's autoloader.
require_once("vendor/autoload.php");

// Bootstrap the application.
$bootstrap = new \MadBans\Bootstrap();
$bootstrap->initialize($f3);