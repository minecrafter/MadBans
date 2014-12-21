<?php

namespace MadBans\Repositories\Plugins\Bat;

use MadBans\Data\Admin;
use MadBans\Data\Ban;
use MadBans\Data\IpAddress;
use MadBans\Data\Player;
use MadBans\Data\Server;
use MadBans\Data\TargetableTrait;
use MadBans\Repositories\BanRepository;
use MadBans\Utilities\PdoHelper;
use MadBans\Utilities\UuidUtilities;

class BatBanRepository implements BanRepository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Creates a ban in the database.
     *
     * @param Ban $ban
     * @return void
     */
    public function createBan(Ban $ban)
    {
        // Are we banning an IP or a player?
        if ($ban->target instanceof IpAddress)
        {
            $query = $this->db->prepare("INSERT INTO `BAT_ban` (ban_ip, ban_staff, ban_server, ban_end, ban_reason)
VALUES (:target, :staff, :server, :expiration, :reason)");
            $query->bindParam(":target", $ban->target->getIp());
        } else if ($ban->target instanceof Player)
        {
            $query = $this->db->prepare("INSERT INTO `BAT_ban` (UUID, ban_staff, ban_server, ban_end, ban_reason)
VALUES (:target, :staff, :server, :expiration, :reason)");
            $query->bindParam(":target", UuidUtilities::createMojangUuid($ban->target->getUuid()->toString()));
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        $query->bindParam(":staff", $ban->admin->getPlayer()->getName());
        $query->bindValue(":server", $ban->server->isGlobal() ? BatConstants::ALL_SERVERS : $ban->server->getName());
        $query->bindValue(":expiration", $ban->expiry ? PdoHelper::dateToPdo($ban->expiry) : FALSE, PDO::PARAM_NULL);
        $query->bindParam(":reason", $ban->reason);

        $query->execute();
    }

    /**
     * Fetches all bans recorded in the database for a target.
     *
     * @param TargetableTrait $target
     * @param Server $server
     * @return array
     */
    public function getBans(TargetableTrait $target, Server $server)
    {
        // Are we banning an IP or a player?
        if ($target instanceof IpAddress)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE ban_ip = :target AND ban_server = :server");
            $query->bindParam(":target", $target->getIp());
        } else if ($target instanceof Player)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE UUID = :target AND ban_server = :server");
            $query->bindParam(":target", $target->getUuid()->toString());
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        $query->bindValue(":server", $server->isGlobal() ? BatConstants::ALL_SERVERS : $server->getName());
        $query->execute();

        $bans = array();

        while ($res = $query->fetch())
        {
            $ban = new Ban;

            $ban->id = $res['ban_id'];
            $ban->admin = $res['ban_staff']; // TODO: we should really IoC in stuff!
            $ban->date = PdoHelper::dateFromPdo($res['ban_begin']);
            $ban->reason = $res['ban_reason'];
            $ban->expiry = $res['ban_end'] ? PdoHelper::dateFromPdo($res['ban_end']) : FALSE;
            $ban->server = $server;
            $ban->rescinded = (int) $res['ban_state'];

            if ($ban->rescinded)
            {
                // Ban was removed
                $ban->rescind_date = PdoHelper::dateFromPdo($res['ban_unbandate']);
                $ban->rescind_reason = $res['ban_unbanreason'];
                $ban->rescinder = $res['ban_unbanstaff'];
            }

            $bans[] = $ban;
        }

        return $bans;
    }

    /**
     * Determines if the player is currently banned.
     *
     * @param TargetableTrait $target
     * @param Server $server
     * @return bool
     */
    public function isCurrentlyBanned(TargetableTrait $target, Server $server)
    {
        // Are we banning an IP or a player?
        if ($target instanceof IpAddress)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE ban_ip = :target AND ban_server = :server AND ban_end < NOW()");
            $query->bindParam(":target", $target->getIp());
        } else if ($target instanceof Player)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE UUID = :target AND ban_server = :server AND ban_end < NOW()");
            $query->bindParam(":target", $target->getUuid()->toString());
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        $query->bindValue(":server", $server->isGlobal() ? BatConstants::ALL_SERVERS : $server->getName());
        $query->execute();

        return $query->columnCount() > 0;
    }

    /**
     * Rescinds the ban for a player. This operation must always
     * leave a "unbanned" marker. You should NOT remove bans from the database.
     *
     * @param Ban $ban
     * @param Admin $admin
     * @param string $reason
     */
    public function rescindBan(Ban $ban, Admin $admin, $reason)
    {
        $query = $this->db->prepare("UPDATE BAT_ban SET ban_state = 0, ban_unbanreason = :reason, ban_unbanstaff = :actor, ban_unbandate = NOW()
WHERE ban_id = :banID AND ban_state = 1;");
        $query->execute([":reason" => $reason, ":actor" => $admin->getPlayer()->getName(), ":banID" => $ban->id]);
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