<?php

namespace MadBans;

use MadBans\Repositories\Plugins\AdminPluginRegistry;
use MadBans\Repositories\Profile\CachingProfileRepository;
use MadBans\Repositories\Profile\OfflineProfileRepository;
use MadBans\Repositories\Web\MbUserProvider;
use MadBans\Settings\SettingsManager;
use MadBans\Utilities\ExternalService;
use MadBans\Utilities\UuidUtilities;
use Silex;
use Symfony\Component\HttpFoundation\Response;
use Twig_SimpleFunction;

class Bootstrap
{
    public function initialize()
    {
        // Configure the application
        $app = new Silex\Application();
        $app['debug'] = true;

        // Initialize database
        $db_options = require(__DIR__ . "/../configuration/db.php");

        if (count($db_options) == 0)
        {
            die("Looks like your database is not set up.");
        }

        $app->register(new Silex\Provider\DoctrineServiceProvider(), array
        (
            'dbs.options' => $db_options,
        ));

        // Initialize URL generator
        $app->register(new Silex\Provider\UrlGeneratorServiceProvider());

        // Initialize custom error handler
        $app->error(function (\Exception $e, $code) use ($app)
        {
            switch ($code) {
                case 404:
                    $message = $app['twig']->render('error/404.twig');
                    break;
                default:
                    return;
            }

            return new Response($message);
        });

        // Initialize Twig
        $app->register(new Silex\Provider\TwigServiceProvider(), array
        (
            'twig.path' => __DIR__ . '/../views',
        ));

        $app['external_service'] = $app->share(function($app)
        {
            return new ExternalService($app);
        });

        $app['twig']->addFunction(new Twig_SimpleFunction('avatar_uri', function ($player, $size) use ($app)
        {
            if (!is_numeric($size))
            {
                throw new \InvalidArgumentException;
            }

            if (!UuidUtilities::validMinecraftUsername($player->getName()))
            {
                throw new \InvalidArgumentException;
            }

            return $app['external_service']->avatarUri($player->getName(), $size);
        }));

        // Initialize security manager
        $app->register(new Silex\Provider\SessionServiceProvider());
        /*$app->register(new Silex\Provider\SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'login' => array(
                    'pattern' => '^/login$',
                    'anonymous' => true
                ),
                'secured' => array(
                    'pattern' => '^.*',
                    'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
                    'logout' => array('logout_path' => '/logout'),
                )
            ),
            'users' => $app->share(function () use ($app) {
                return new MbUserProvider($app['db']);
            }),
        ))*/;

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
            return new CachingProfileRepository($plugin->getProfileRepository($app), $app['db']);
        });

        $app['lookup_repository'] = $app->share(function($app) use ($plugin)
        {
            // CachingProfileRepository implements our lookup repository.
            return $app['profile_repository'];
        });

        $app['ban_repository'] = $app->share(function($app) use ($plugin)
        {
            return $plugin->getBanRepository($app);
        });

        $app->get('/', 'MadBans\Controllers\IndexController::index')->bind('home');
        $app->get('/a/_lookahead_player', 'MadBans\Controllers\PlayerController::lookahead')->bind('player_lookahead');
        $app->get('/a/_lookup_submit', 'MadBans\Controllers\PlayerController::lookupSubmit')->bind('lookup_submit');
        $app->get('/p/{player}', 'MadBans\Controllers\PlayerController::find')->bind('player_info');

        return $app;
    }
}