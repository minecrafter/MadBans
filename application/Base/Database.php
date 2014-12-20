<?php

namespace MadBans\Base;

use DB;

class Database
{
    public $db;

    public function __construct($configuration)
    {
        $this->db = new DB\SQL(
            $configuration['dsn'],
            $configuration['username'],
            $configuration['password']
        );
    }
}