<?php

namespace MadBans\Settings;

use Silex;

/**
 * The SettingsManager is a fairly simple wrapper around DBAL for commonly-set settings.
 *
 * @package MadBans\Settings
 */
class SettingsManager
{
    private $db;
    private $cache = array();

    public function __construct(Silex\Application $app)
    {
        $this->db = $app['db'];
    }

    public function get($setting, $default = FALSE)
    {
        if (array_key_exists($setting, $this->cache))
        {
            return $this->cache['setting'];
        }

        $result = $this->db->createQueryBuilder()
            ->select('value')
            ->from('settings', 'settings')
            ->where('settings.setting = ?')
            ->setParameter(0, $setting)
            ->execute();

        $final = $result ? $result->fetch()['value'] : $default;
        $this->cache[$setting] = $final;

        return $final;
    }

    public function set($setting, $value)
    {
        $this->cache['setting'] = $value;

        $this->db->createQueryBuilder()
            ->update('settings')
            ->set('setting', '?')
            ->set('value', '?')
            ->setParameter(0, $setting)
            ->setParameter(1, $value)
            ->execute();
    }

    public function allSet()
    {
        $result = $this->db->fetchAll("SELECT setting, value FROM settings");

        $flattened = array();

        foreach ($result as $value)
        {
            $flattened[$value['setting']] = $value['value'];
        }

        return $flattened;
    }
}