<?php

namespace MadBans\Controllers;

use Silex;
use Symfony\Component\HttpFoundation\Request;

class BanController
{
    public function viewBan(Silex\Application $app, Request $request, $id)
    {
        if (!$id)
        {
            $app->abort(404);
        }

        $ban = $app['ban_repository']->getBan($id);

        if (!$ban)
        {
            $app->abort(404);
        }

        return $app['twig']->render('ban/info.twig', ['ban' => $ban]);
    }
}