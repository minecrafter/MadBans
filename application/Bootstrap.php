<?php

namespace MadBans;

use Carbon\Carbon;
use MadBans\Repositories\Plugins\AdminPluginRegistry;
use MadBans\Repositories\Profile\CachingProfileRepository;
use MadBans\Security\MadBansRoles;
use MadBans\Services\MadBansServiceProvider;
use MadBans\Settings\SettingsManager;
use MadBans\Utilities\ExternalService;
use MadBans\Utilities\UuidUtilities;
use ReflectionClass;
use Silex;
use SimpleUser;
use Symfony\Component\HttpFoundation\Response;
use Twig_SimpleFunction;

class Bootstrap
{
    public function initialize()
    {
        // Configure the application
        $app = new Silex\Application();
        $app['debug'] = true;

        // Determine if we have any configuration
        $install_mode = FALSE;

        if (!file_exists(__DIR__ . "/../configuration/site.php") ||
            !file_exists(__DIR__ . "/../configuration/db.php"))
        {
            $install_mode = TRUE;
        }

        // Initialize configuration
        $db_options = include(__DIR__ . "/../configuration/db.php");
        $site_options = include(__DIR__ . "/../configuration/site.php");

        // Initialize all services
        $app->register(new Silex\Provider\DoctrineServiceProvider(), array
        (
            'dbs.options' => $db_options,
        ));
        // Initialize our handler.
        $app->register(new MadBansServiceProvider($site_options));
        $app->register(new Silex\Provider\TwigServiceProvider(), array
        (
            'twig.path' => __DIR__ . '/../views',
        ));
        $app->register(new Silex\Provider\SessionServiceProvider());
        $app->register(new Silex\Provider\UrlGeneratorServiceProvider());

        $app->boot();

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

        $app->get('/', 'MadBans\Controllers\IndexController::index')->bind('home');

        /* Player Information */
        $app->get('/a/_lookahead_player', 'MadBans\Controllers\PlayerController::lookahead')->bind('player_lookahead');
        $app->get('/a/_lookup_submit', 'MadBans\Controllers\PlayerController::lookupSubmit')->bind('lookup_submit');
        $app->get('/p/{player}', 'MadBans\Controllers\PlayerController::find')->bind('player_info');

        /* Ban Information */
        $app->get('/b/{id}', 'MadBans\Controllers\BanController::viewBan')->bind('ban_info');

        /* Admin Controllers */

        /* Secured Routes */
        // TODO: Uncomment once a proper user system is working
        /*$app['security.access_rules'] = array(
            array('^/auth/login(|_check)$', ''), // We need to allow authentication
            array('^.*$', 'ROLE_USER'), // User access

            // Player Information
            array('^/p/.*$', MadBansRoles::VIEW_PLAYER_INFORMATION),
            // NB: These routes are secured as they expose information on players
            array('/a/_lookahead_player', MadBansRoles::VIEW_PLAYER_INFORMATION),
            array('/a/_lookup_submit', MadBansRoles::VIEW_PLAYER_INFORMATION),
        );*/

        return $app;
    }
}