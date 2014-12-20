<?php

namespace MadBans;

use Base;
use MadBans\Base\Database;

class Bootstrap
{
    private static $initialized = false;
    public static $database;

    public static function initialize()
    {
        if (self::$initialized)
        {
            throw new \Exception("MadBans already initialized");
        }

        self::$initialized = TRUE;

        $f3 = Base::instance();

        // Configure MadBans database.
        self::$database = new Database($f3->get('madbans.local_db'));

        // Configure F3 routes.
        $f3 = Base::instance();
        $f3->route('GET /', 'MadBans\Controllers\IndexController::index');
        return $f3->run();
    }
}