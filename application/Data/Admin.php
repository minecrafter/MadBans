<?php

namespace MadBans\Data;

use Symfony\Component\Security\Core\User\User;

class Admin
{
    private $player;
    private $madbans_user;

    public function __construct(Player $player, User $madbans_admin)
    {
        $this->player = $player;
        $this->madbans_admin = $madbans_admin;
    }

    /**
     * Returns the in-game association for this admin. Used for in-game attribution.
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Returns the MadBans user for this admin.
     *
     * @return \Symfony\Component\Security\Core\User\User
     */
    public function getMadbansUser()
    {
        return $this->madbans_user;
    }
}