<?php

namespace MadBans\Data;

class Admin
{
    public $player;
    public $madbans_admin;

    public function __construct(Player $player, $madbans_admin)
    {
        $this->player = $player;
        $this->madbans_admin = $madbans_admin;
    }
}