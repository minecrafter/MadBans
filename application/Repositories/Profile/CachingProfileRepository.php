<?php

namespace MadBans\Repositories\Profile;

use Carbon\Carbon;
use MadBans\Data\Player;
use MadBans\Repositories\PlayerLookupRepository;
use MadBans\Repositories\ProfileRepository;
use MadBans\Utilities\PdoHelper;
use PDO;

/**
 * CachingProfileRepository uses a database-backed UUID cache. While somewhat slow, this is needed to avoid
 * Mojang API ratelimiting and enable reverse player name lookups for offline-mode players.
 *
 * CachingProfileRepository by default caches associations for three days.
 *
 * @package MadBans\Repositories\Profile
 */
class CachingProfileRepository implements ProfileRepository, PlayerLookupRepository
{
    private $delegate;
    private $db;

    public function __construct(ProfileRepository $delegate, $db)
    {
        $this->delegate = $delegate;
        $this->db = $db;
    }

    /**
     * Resolves a player using a UUID.
     *
     * @param $uuid string
     * @return \MadBans\Data\Player
     */
    public function byUuid($uuid)
    {
        $query = $this->db->prepare('SELECT name FROM cached_players WHERE uuid = :uuid AND expires > NOW()');
        $query->execute([':uuid' => $uuid]);

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result)
        {
            return Player::fromNameAndUuid($result['name'], $uuid);
        } else
        {
            $internal_result = $this->delegate->byUuid($uuid);
            if ($internal_result)
            {
                $this->persist($internal_result);
                return $internal_result;
            } else
            {
                return FALSE;
            }
        }
    }

    /**
     * Resolves a player using a username.
     *
     * @param $username string
     * @return \MadBans\Data\Player
     */
    public function byUsername($username)
    {
        $query = $this->db->prepare('SELECT name, uuid FROM cached_players WHERE name = :name AND expires > NOW()');
        $query->execute([':name' => $username]);

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result)
        {
            return Player::fromNameAndUuid($result['name'], $result['uuid']);
        } else
        {
            $internal_result = $this->delegate->byUsername($username);
            if ($internal_result)
            {
                $this->persist($internal_result);
                return $internal_result;
            } else
            {
                return FALSE;
            }
        }
    }

    private function persist(Player $player)
    {
        $expires = Carbon::now()->addDays(3);

        $query = $this->db->prepare('INSERT INTO cached_players (uuid, name, expires) VALUES (:uuid, :name, :expires)');
        $query->execute([':uuid' => $player->getUuid()->toString(), 'name' => $player->getName(), 'expires' => PdoHelper::dateToPdo($expires)]);
    }

    /**
     * Attempts a lookup of possible players.
     *
     * @param string $term
     * @return array
     */
    public function search($term)
    {
        $query = $this->db->prepare('SELECT name, uuid FROM cached_players WHERE name LIKE :term AND expires > NOW() LIMIT 10');
        $query->execute([':term' => '%' . $term . '%']);

        return $query->fetchAll(PDO::FETCH_ASSOC) ?: array();
    }
}