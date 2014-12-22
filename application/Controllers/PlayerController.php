<?php
/**
 * Created by PhpStorm.
 * User: tux
 * Date: 12/20/14
 * Time: 7:22 PM
 */

namespace MadBans\Controllers;

use MadBans\Data\AjaxResponse;
use MadBans\Security\MadBansRoles;
use MadBans\Utilities\UuidUtilities;
use Rhumsaa\Uuid\Uuid;
use Silex;
use Symfony\Component\HttpFoundation\Request;

class PlayerController
{
    public function find(Silex\Application $app, Request $request, $player)
    {
        // Try getting a player out
        $p = FALSE;

        if (UuidUtilities::validMinecraftUsername($player))
        {
            $p = $app['profile_repository']->byUsername($player);
        } else
        {
            // Maybe it's a UUID
            if (preg_match_all('/' . UuidUtilities::VALID_MOJANG_UUID . '/', $player))
            {
                $uuid = UuidUtilities::createJavaUuid($player);
                $p = $app['profile_repository']->byUuid($uuid);
            } elseif (Uuid::isValid($player))
            {
                $uuid = $player;
                $p = $app['profile_repository']->byUuid($uuid);
            }
        }

        if (!$p)
        {
            if ($request->isXmlHttpRequest())
            {
                $app->abort(404, $app->json(new AjaxResponse('The specified player could not be found.', NULL)));
            } else
            {
                $app->abort(404, "Player not found");
            }
        }

        // Query bans, if we can view them.
        /*if ($app['user'] && $app['security']->isGranted(MadBansRoles::VIEW_BAN_INFORMATION))
        {
            $bans = $app['ban_repository']->getBans($p, FALSE);
        } else
        {
            $bans = array();
        }*/

        // TODO: Uncomment above section when security is sufficiently working.
        $bans = $app['ban_repository']->getBans($p, NULL);

        return $app['twig']->render('player/player.twig', ['player' => $p, 'bans' => $bans]);
    }

    public function lookahead(Silex\Application $app, Request $request)
    {
        $term = $request->get('term');

        if (!$term)
        {
            $app->abort(404);
        }

        return $app->json($app['lookup_repository']->search($term));
    }

    public function lookupSubmit(Silex\Application $app, Request $request)
    {
        $term = $request->get('term');

        if (!$term)
        {
            $app->abort(404);
        }

        if (strlen($term) < 2)
        {
            // term is too short
            $app->abort(404);
        }

        $search = $app['lookup_repository']->search($term);

        if (count($search) > 0)
        {
            return $app->redirect($app['url_generator']->generate('player_info', ['player' => $search[0]['name']]));
        }

        $app->abort(404);
    }
}