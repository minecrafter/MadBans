<?php

namespace MadBans\Repositories;

/**
 * A ProfileRepository implements a factory of @see MadBans\Data\Player objects.
 *
 * @package MadBans\Repositories
 */
interface ProfileRepository
{
    /**
     * Resolves a player using a UUID.
     *
     * @param $uuid string
     * @return \MadBans\Data\Player
     */
    public function byUuid($uuid);

    /**
     * Resolves a player using a username.
     *
     * @param $username string
     * @return \MadBans\Data\Player
     */
    public function byUsername($username);
}