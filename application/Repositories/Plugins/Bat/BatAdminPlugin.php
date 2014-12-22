<?php

namespace MadBans\Repositories\Plugins\Bat;

use MadBans\Repositories\Plugins\AdminPlugin;
use MadBans\Repositories\Profile\MojangProfileRepository;
use Silex;

class BatAdminPlugin implements AdminPlugin
{
    /**
     * Fetch the profile repository used.
     *
     * @param Silex\Application $app
     * @return \MadBans\Repositories\ProfileRepository
     */
    public function getProfileRepository(Silex\Application $app)
    {
        //return new BatProfileRepository($app['dbs']['bat']);
        return new MojangProfileRepository();
    }

    /**
     * Fetch the banning repository used.
     *
     * @param Silex\Application $app
     * @return \MadBans\Repositories\BanRepository
     */
    public function getBanRepository(Silex\Application $app)
    {
        return new BatBanRepository($app['dbs']['bat'], $app['profile_repository']);
    }
}