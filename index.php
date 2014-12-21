<?php

/**
 * MadBans: Flexible web administration interface for many Minecraft banning plugins.
 *
 * Copyright (C) 2014 Tux. Licensed under MIT license.
 */

// Require Composer's autoloader.
require_once("vendor/autoload.php");

// If we're using the CLI server, we might be serving a file.
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// Bootstrap the application.
$bootstrap = new \MadBans\Bootstrap();
$bootstrap->initialize()->run();