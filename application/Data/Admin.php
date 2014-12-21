<?php

namespace MadBans\Data;

class Admin
{
    private $player;
    private $madbans_user;

    public function __construct(Player $player, $madbans_admin)
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
     * @return mixed
     */
    public function getMadbansUser()
    {
        return $this->madbans_user;
    }


}