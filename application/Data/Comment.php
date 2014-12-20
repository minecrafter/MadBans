<?php

namespace MadBans\Data;

class Comment
{
    use TargetableTrait;

    public $admin;
    public $message;
    public $date;
}