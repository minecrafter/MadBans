<?php

namespace MadBans\Data;

class Ban
{
    use RescindableTrait, TargetableTrait;

    public $id;
    public $admin;
    public $date;
    public $reason;
    public $expiry;
}