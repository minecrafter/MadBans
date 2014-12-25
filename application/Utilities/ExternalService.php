<?php

namespace MadBans\Utilities;

class ExternalService
{
    private $site_config;

    public function __construct($site_config)
    {
        $this->site_config = $site_config;
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
        if ($this->site_config['offline_mode'])
            return "/img/steve_head.png";

        return "//cravatar.eu/avatar/" . $username . "/" . $size;
    }
}