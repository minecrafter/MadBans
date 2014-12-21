<?php
/**
 * Created by PhpStorm.
 * User: tux
 * Date: 12/20/14
 * Time: 7:22 PM
 */

namespace MadBans\Controllers;

use MadBans\Data\AjaxResponse;
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
                $p = $app['profile_repository']->byUuid($player);
            } elseif (Uuid::isValid($player))
            {
                $uuid = $player;
                $p = $app['profile_repository']->byUuid($player);
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

        // Player is out of our way.
        // TODO: Add in banning stuff!
        return $app['twig']->render('player/player.twig', ['player' => $p]);
    }
}