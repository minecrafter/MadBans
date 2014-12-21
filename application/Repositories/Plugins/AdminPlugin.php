<?php

namespace MadBans\Repositories\Plugins;

use Silex;

interface AdminPlugin
{
    /**
     * Fetch the profile repository used.
     *
     * @param Silex\Application $app
     * @return \MadBans\Repositories\ProfileRepository
     */
    public function getProfileRepository(Silex\Application $app);

    /**
     * Fetch the banning repository used.
     *
     * @param Silex\Application $app
     * @return \MadBans\Repositories\BanRepository
     */
    public function getBanRepository(Silex\Application $app);
}