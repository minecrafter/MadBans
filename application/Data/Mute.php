<?php

namespace MadBans\Data;

class Mute
{
    use RescindableTrait, TargetableTrait, ExpirableTrait;

    public $id;
    public $admin;
    public $date;
    public $reason;
}