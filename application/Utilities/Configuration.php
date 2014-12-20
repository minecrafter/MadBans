<?php

namespace MadBans\Utilities;

class Configuration
{
    public static function isOfflineMode()
    {
        $madbans_config = \Base::instance()->get("madbans");

        if (array_key_exists("offline_mode", $madbans_config))
        {
            return $madbans_config["offline_mode"];
        }

        return FALSE;
    }
}