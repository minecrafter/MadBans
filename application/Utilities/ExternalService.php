<?php

namespace MadBans\Utilities;

class ExternalService
{
    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Generates an avatar URL. Offline mode servers get the Steve head, others get a Cravatar link.
     *
     * @param string $username
     * @param int $size
     * @return string
     */
    public function avatarUri($username, $size = 32)
    {
        if ($this->app['settings_manager']->get('offline_mode'))
            return "/img/steve_head.png";

        return "//cravatar.eu/avatar/" . $username . "/" . $size;
    }
}