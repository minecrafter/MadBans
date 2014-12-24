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
use PDO;

class BatBanRepository implements BanRepository
{
    private $db;
    private $profile;

    public function __construct($db, $profile)
    {
        $this->db = $db;
        $this->profile = $profile;
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
     * Fetches a ban from the database.
     *
     * @param integer $id
     * @return Ban
     */
    public function getBan($id)
    {
        $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE ban_id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        return $result ? $this->deserializeBan($result) : NULL;
    }

    /**
     * Fetches all bans recorded in the database for a target, ordered by ban date.
     *
     * @param $target
     * @param $server
     * @return array
     */
    public function getBans($target, $server)
    {
        $add = !$server ? "" : "AND ban_server = :server ";

        if ($target instanceof IpAddress)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE ban_ip = :target " . $add . "ORDER BY ban_begin");
            $query->bindParam(":target", $target->getIp());
        } else if ($target instanceof Player)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE UUID = :target " . $add . "ORDER BY ban_begin");
            $query->bindParam(":target", UuidUtilities::createMojangUuid($target->getUuid()));
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        if ($server)
        {
            $query->bindValue(":server", $server->isGlobal() ? BatConstants::ALL_SERVERS : $server->getName());
        }

        $query->execute();

        $bans = array();

        while ($res = $query->fetch())
        {
            $bans[] = $this->deserializeBan($res);
        }

        return $bans;
    }

    /**
     * Determines if the player is currently banned.
     *
     * @param $target
     * @param $server
     * @return bool
     */
    public function isCurrentlyBanned($target, $server)
    {
        $add = !$server ? "" : " AND ban_server = :server";

        if ($target instanceof IpAddress)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE ban_ip = :target AND ban_end < NOW()" . $add);
            $query->bindParam(":target", $target->getIp());
        } else if ($target instanceof Player)
        {
            $query = $this->db->prepare("SELECT * FROM `BAT_ban` WHERE UUID = :target AND ban_end < NOW()" . $add);
            $query->bindParam(":target", UuidUtilities::createMojangUuid($target->getUuid()));
        } else
        {
            throw new \InvalidArgumentException("target is not an IpAddress or Player");
        }

        if ($server)
        {
            $query->bindValue(":server", $server->isGlobal() ? BatConstants::ALL_SERVERS : $server->getName());
        }

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

    private function deserializeBan($res)
    {
        $ban = new Ban;

        $ban->id = $res['ban_id'];
        $ban->admin = $res['ban_staff']; // TODO: we should really IoC in stuff!
        $ban->date = PdoHelper::dateFromPdo($res['ban_begin']);
        $ban->reason = $res['ban_reason'];
        $ban->expiry = $res['ban_end'] ? PdoHelper::dateFromPdo($res['ban_end']) : FALSE;

        if ($res['ban_server'] === BatConstants::ALL_SERVERS)
        {
            $ban->server = Server::matchAll();
        } else
        {
            $ban->server = Server::create($res['ban_server']);
        }

        $ban->rescinded = !$res['ban_state'];

        if ($ban->rescinded)
        {
            // Ban was removed
            $ban->rescind_date = PdoHelper::dateFromPdo($res['ban_unbandate']);
            $ban->rescind_reason = $res['ban_unbanreason'];
            $ban->rescinder = $res['ban_unbanstaff'];
        }

        if ($res['UUID'])
        {
            $ban->target = $this->profile->byUuid(UuidUtilities::createJavaUuid($res['UUID']));
        } else if ($res['ban_ip'])
        {
            $ban->target = IpAddress::fromIpv4($res['ban_ip']);
        }

        return $ban;
    }
}