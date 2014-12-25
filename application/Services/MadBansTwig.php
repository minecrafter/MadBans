<?php

namespace MadBans\Services;

use InvalidArgumentException;
use MadBans\Utilities\UuidUtilities;
use Silex\Application;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class MadBansTwig extends Twig_Extension
{
    private $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "madbans";
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('avatar_uri', function ($player, $size)
            {
                if (!is_numeric($size))
                {
                    throw new InvalidArgumentException;
                }

                if (!UuidUtilities::validMinecraftUsername($player->getName()))
                {
                    throw new InvalidArgumentException;
                }

                return $this->app['external_service']->avatarUri($player->getName(), $size);
            })
        ];
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('datediff', function ($date)
            {
                return $date->diffForHumans();
            })
        ];
    }

}