<?php

namespace MadBans\Data;

class Mute
{
    use RescindableTrait, TargetableTrait;

    public $id;
    public $admin;
    public $date;
    public $reason;
    public $expiry;
}