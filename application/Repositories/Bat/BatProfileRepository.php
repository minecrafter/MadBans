<?php

namespace MadBans\Repositories\Bat;

use MadBans\Base\Database;
use MadBans\Data\Player;
use MadBans\Repositories\ProfileRepository;
use MadBans\Utilities\UuidUtilities;

class BatProfileRepository implements ProfileRepository
{
    private $bat_db;

    public function __construct(Database $database)
    {
        $this->bat_db = $database->db;
    }

    /**
     * Resolves a player using a UUID.
     *
     * @param $uuid string
     * @return \MadBans\Data\Player
     */
    public function byUuid($uuid)
    {
        $query = $this->bat_db->prepare('SELECT BAT_player FROM BAT_players WHERE UUID = :uuid');
        $query->execute([':uuid' => UuidUtilities::createMojangUuid($uuid)]);

        $result = $query->fetch();

        if ($result)
        {
            return Player::fromNameAndUuid($result[0], $uuid);
        }

        // We didn't find them in the database
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
        $query = $this->bat_db->prepare('SELECT UUID FROM BAT_players WHERE BAT_player = :player');
        $query->execute([':player' => $username]);

        $result = $query->fetch();

        if ($result)
        {
            return Player::fromNameAndUuid($username, UuidUtilities::createJavaUuid($result[0]));
        }

        // We didn't find them in the database
        return FALSE;
    }
}