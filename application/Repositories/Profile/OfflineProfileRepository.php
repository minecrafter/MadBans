<?php

namespace MadBans\Repositories\Profile;

use Exception;
use MadBans\Data\Player;
use MadBans\Repositories\ProfileRepository;
use MadBans\Utilities\UuidUtilities;

/**
 * A OfflineProfileRepository implements an offline-mode repository. Due to the limitations of offline mode,
 * you can only turn a username into a UUID.
 *
 * @package MadBans\Repositories\Profile
 */
class OfflineProfileRepository implements ProfileRepository
{
    /**
     * Resolves a player using a UUID.
     *
     * @param $uuid string
     * @return \MadBans\Data\Player
     */
    public function byUuid($uuid)
    {
        // Unfortunately, due to the way offline mode works, you can't turn a offline-mode UUID into a username.
        return FALSE;
    }

    /**
     * Resolves a player using a username.
     *
     * @param $username string
     * @return \MadBans\Data\Player
     */
    public function byUsername($username)
    {
        return Player::fromNameAndUuid($username, UuidUtilities::constructOfflinePlayerUuid($username));
    }
}