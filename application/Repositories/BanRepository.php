<?php

namespace MadBans\Repositories;

use MadBans\Data\Ban;
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
     * Fetches all bans recorded in the database for a target.
     *
     * @param TargetableTrait $target
     * @return array
     */
    public function getBans(TargetableTrait $target);

    /**
     * Determines if the target is currently banned.
     *
     * @param TargetableTrait $target
     * @return bool
     */
    public function isCurrentlyBanned(TargetableTrait $target);

    /**
     * Rescinds the ban for a player. This operation must always
     * leave a "unbanned" marker. You should NOT remove bans from the database.
     *
     * @param Ban $ban
     */
    public function rescindBan(Ban $ban);

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