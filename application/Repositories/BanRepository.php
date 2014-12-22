<?php

namespace MadBans\Repositories;

use MadBans\Data\Admin;
use MadBans\Data\Ban;
use MadBans\Data\Server;
use MadBans\Data\TargetableTrait;

/**
 * A BanRepository implements a interface for creating, removing, and getting bans.
 */
interface BanRepository
{
    /**
     * Creates a ban in the database.
     *
     * @param Ban $ban
     * @return void
     */
    public function createBan(Ban $ban);

    /**
     * Fetches a ban from the database.
     *
     * @param integer $id
     * @return Ban
     */
    public function getBan($id);

    /**
     * Fetches all bans recorded in the database for a target, ordered by ban date.
     *
     * @param $target
     * @param $server
     * @return array
     */
    public function getBans($target, $server);

    /**
     * Determines if the target is currently banned.
     *
     * @param $target
     * @param Server $server
     * @return bool
     */
    public function isCurrentlyBanned($target, $server);

    /**
     * Rescinds the ban for a player. This operation must always
     * leave a "unbanned" marker. You should NOT remove bans from the database.
     *
     * @param Ban $ban
     * @param Admin $admin
     * @param string $reason
     */
    public function rescindBan(Ban $ban, Admin $admin, $reason);

    /**
     * Determines whether or not this repository accepts player bans.
     *
     * @return bool
     */
    public function supportsPlayerBans();

    /**
     * Determines whether or not this repository accepts IP bans.
     *
     * @return bool
     */
    public function supportsIpBans();

    /**
     * Determines whether or not this repository accepts server bans.
     *
     * @return bool
     */
    public function supportsServerSpecificBans();
}