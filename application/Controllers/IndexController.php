<?php

namespace MadBans\Controllers;

use Silex;

class IndexController
{
    public function index(Silex\Application $app)
    {
        return $app["profile_repository"]->byUsername("tuxed")->getUuid()->toString();
    }
}