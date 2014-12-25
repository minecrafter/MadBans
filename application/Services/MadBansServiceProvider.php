<?php
/**
 * Created by PhpStorm.
 * User: tux
 * Date: 12/25/14
 * Time: 1:04 AM
 */

namespace MadBans\Services;

use MadBans\Repositories\Profile\CachingProfileRepository;
use MadBans\Repositories\Profile\ChainingProfileRepository;
use MadBans\Repositories\Profile\MojangProfileRepository;
use MadBans\Utilities\ExternalService;
use ReflectionClass;
use Silex\Application;
use Silex\ServiceProviderInterface;

class MadBansServiceProvider implements ServiceProviderInterface
{
    private $plugin;
    private $site_config;

    function __construct($site_config)
    {
        $this->site_config = $site_config;
    }

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        // Does the admin plugin exist?
        try
        {
            $pluginClass = new ReflectionClass($this->site_config['admin_plugin']);

            if (!$pluginClass->isSubclassOf('MadBans\Repositories\Plugins\AdminPlugin'))
            {
                throw new \ReflectionException('Admin plugin ' . $this->site_config['admin_plugin'] . ' does not '
                        . 'implement MadBans\Repositories\Plugins\AdminPlugin');
            }

            $this->plugin = $pluginClass->newInstanceWithoutConstructor();
        } catch (\ReflectionException $e)
        {
            // Unfortunately, there is no way to actually deliver this sort of error to the client.
            // At this point, we must die.
            //die('The banning plugin is misconfigured.');
            $app->abort(500, $e);
        }

        // Other services have to be initialized during boot instead.
        $app['external_service'] = $app->share(function($app)
        {
            return new ExternalService($this->site_config);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // Add our Twig extension
        $app['twig']->addExtension(new MadBansTwig($app));

        // Provide services not registered during registration
        $app['profile_repository'] = $app->share(function($app)
        {
            return new CachingProfileRepository(
                new ChainingProfileRepository([$this->plugin->getProfileRepository($app),
                    new MojangProfileRepository()]), $app['db']);
        });

        $app['lookup_repository'] = $app->share(function($app)
        {
            // CachingProfileRepository implements our lookup repository.
            return $app['profile_repository'];
        });

        $app['ban_repository'] = $app->share(function($app)
        {
            return $this->plugin->getBanRepository($app);
        });
    }
}