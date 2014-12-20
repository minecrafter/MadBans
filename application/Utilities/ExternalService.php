<?php

namespace MadBans\Utilities;

class ExternalService
{
    public static function avatarUri($username, $size = 32)
    {
        if (Configuration::isOfflineMode())
            return "/img/steve_head.png";

        return "http://cravatar.eu/avatar/" . $username . "/" . $size;
    }
}