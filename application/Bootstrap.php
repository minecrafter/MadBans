<?php

namespace MadBans;

use MadBans\Repositories\Plugins\AdminPluginRegistry;
use MadBans\Repositories\Profile\OfflineProfileRepository;
use MadBans\Settings\SettingsManager;
use Silex;

class Bootstrap
{
    public function initialize()
    {
        // Configure the application
        $app = new Silex\Application();
        $app['debug'] = true;

        // Initialize database
        $db_options = require(basename(__FILE__) . "/../configuration/db.php");

        if (count($db_options) == 0)
        {
            die("Looks like your database is not set up.");
        }

        $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
            'dbs.options' => $db_options,
        ));

        // Initialize settings manager
        $app['settings_manager'] = $app->share(function($app)
        {
            return new SettingsManager($app);
        });

        // Now we have enough infrastructure running to determine everything else
        $banning_plugin_backend = $app['settings_manager']->get('backend');
        $manager = new AdminPluginRegistry();
        $plugin = $manager->get($banning_plugin_backend);

        if (!$plugin)
        {
            die("The specified banning plugin backend " . $banning_plugin_backend . " is no longer valid.");
        }

        $app['profile_repository'] = $app->share(function($app) use ($plugin)
        {
            $offline_mode = $app['settings_manager']->get('offline_mode');

            if ($offline_mode)
            {
                return new OfflineProfileRepository();
            } else
            {
                return $plugin->getProfileRepository($app);
            }
        });

        $app['ban_repository'] = $app->share(function($app) use ($plugin)
        {
            return $plugin->getBanRepository($app);
        });

        $app->get('/', 'MadBans\Controllers\IndexController::index')->bind('home');

        return $app;
    }
}