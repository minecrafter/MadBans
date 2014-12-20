<?php

namespace MadBans\Repositories\Profile;

use MadBans\Data\Player;
use MadBans\Repositories\ProfileRepository;
use MadBans\Utilities\UuidUtilities;
use Navarr\Minecraft\Profile;

/**
 * The MojangProfileRepository class implements a @see MadBans\Repositories\ProfileRepository
 * that fetches username information from Mojang's public profile APIs.
 *
 * As these APIs are rate-limited, these should be cached.
 *
 * @package MadBans\Repositories\Profile
 */
class MojangProfileRepository implements ProfileRepository
{
    /**
     * Resolves a player using a UUID.
     *
     * @param $uuid string
     * @return \MadBans\Data\Player
     */
    public function byUuid($uuid)
    {
        $profile = Profile::fromUuid(UuidUtilities::createMojangUuid($uuid));
        return Player::fromNameAndUuid($profile->name, $uuid);
    }

    /**
     * Resolves a player using a username.
     *
     * @param $username string
     * @return \MadBans\Data\Player
     */
    public function byUsername($username)
    {
        $profile = Profile::fromUsername($username);
        return Player::fromNameAndUuid($profile->name, UuidUtilities::createJavaUuid($profile->uuid));
    }
}