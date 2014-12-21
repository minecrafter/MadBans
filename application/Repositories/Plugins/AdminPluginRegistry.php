<?php

namespace MadBans\Repositories\Plugins;

use MadBans\Repositories\Plugins\Bat\BatAdminPlugin;

class AdminPluginRegistry
{
    private $plugins = array();

    function __construct()
    {
        $this->plugins['bat'] = new BatAdminPlugin();
    }

    /**
     * @param $plugin
     * @return \MadBans\Repositories\Plugins\AdminPlugin
     */
    public function get($plugin)
    {
        return $this->plugins[$plugin];
    }
}