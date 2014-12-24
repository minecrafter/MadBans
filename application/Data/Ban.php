<?php

namespace MadBans\Data;

class Ban
{
    use RescindableTrait, TargetableTrait, ExpirableTrait;

    public $id;
    public $admin;
    public $date;
    public $reason;
    public $server;

    public function getStatus()
    {

    }
}