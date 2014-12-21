<?php

/**
 * MadBans: Flexible web administration interface for many Minecraft banning plugins.
 *
 * Copyright (C) 2014 Tux. Licensed under MIT license.
 */

// Require Composer's autoloader.
require_once("vendor/autoload.php");

// Bootstrap the application.
$bootstrap = new \MadBans\Bootstrap();
$bootstrap->initialize()->run();