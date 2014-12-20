<?php

namespace MadBans\Repositories\Bat;

use MadBans\Base\Database;
use MadBans\Data\Ban;
use MadBans\Data\IpAddress;
use MadBans\Data\Player;
use MadBans\Data\TargetableTrait;
use MadBans\Repositories\BanRepository;

class BatBanRepository implements BanRepository
{
    private $bat_db;

    public function __construct(Database $database)
    {
        $this->bat_db = $database->db;
    }

    /**
     * Creates a ban in the database.
     *
     * @param Ban $ban
     * @return void
     */
    public function createBan(Ban $ban)
    {
        $php_sucks = FALSE;

        // Are we banning an IP or a player?
        if ($ban->target instanceof IpAddress)
        {
            $query = $this->bat_db->prepare("INSERT INTO `BAT_ban` (ban_ip, ban_staff, ban_server, ban_end, ban_reason)
VALUES (:target, :staff, :server, :expiration, :reason)");
            $query->bindParam(":target", $ban->target->ip);
        } else if ($ban->target instanceof Player)
        {
            $query = $this->bat_db->prepare("INSERT INTO `BAT_ban` (UUID, ban_staff, ban_server, ban_end, ban_reason)
VALUES (:target, :staff, :server, :expiration, :reason)");
            $query->bindParam(":target", $ban->target->uuid);
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        $query->bindParam(":staff", $ban->admin->player->name);
        $query->bindParam(":server", $php_sucks, PDO::PARAM_NULL); // TODO: we should support per-server bans!
        $query->bindParam(":expiration", $expiry, PDO::PARAM_NULL);
        $query->bindParam(":reason", $reason);

        $query->execute();
    }

    /**
     * Fetches all bans recorded in the database for a target.
     *
     * @param TargetableTrait $target
     * @return array
     */
    public function getBans(TargetableTrait $target)
    {
        // Are we banning an IP or a player?
        if ($target instanceof IpAddress)
        {
            $query = $this->bat_db->prepare("SELECT * FROM `BAT_ban` WHERE ban_ip = :target");
            $query->bindParam(":target", $target->ip);
        } else if ($target instanceof Player)
        {
            $query = $this->bat_db->prepare("SELECT * FROM `BAT_ban` WHERE UUID = :target");
            $query->bindParam(":target", $target->uuid);
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        $query->execute();
    }

    /**
     * Determines if the player is currently banned.
     *
     * @param TargetableTrait $target
     * @return bool
     */
    public function isCurrentlyBanned(TargetableTrait $target)
    {
        // TODO: Implement isCurrentlyBanned() method.
    }

    /**
     * Rescinds the ban for a player. This operation must always
     * leave a "unbanned" marker. You should NOT remove bans from the database.
     *
     * @param Ban $ban
     */
    public function rescindBan(Ban $ban)
    {
        // TODO: Implement rescindBan() method.
    }

    /**
     * Determines whether or not this repository accepts player bans.
     *
     * @return bool
     */
    public function supportsPlayerBans()
    {
        return TRUE;
    }

    /**
     * Determines whether or not this repository accepts IP bans.
     *
     * @return bool
     */
    public function supportsIpBans()
    {
        return TRUE;
    }

    /**
     * Determines whether or not this repository accepts server bans.
     *
     * @return bool
     */
    public function supportsServerSpecificBans()
    {
        return TRUE;
    }
}