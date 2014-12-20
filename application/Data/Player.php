<?php

namespace MadBans\Data;

use MadBans\Utilities\UuidUtilities;
use Rhumsaa\Uuid\Uuid;
use InvalidArgumentException;

class Player
{
    public $name;
    public $uuid;

    private function __construct($name, $uuid)
    {
        $this->name = $name;
        $this->uuid = $uuid;
    }

    public static function fromNameAndUuid($name, $uuid)
    {
        if (!Uuid::isValid($uuid))
        {
            throw new InvalidArgumentException("UUID is not valid");
        }

        if (!UuidUtilities::validMinecraftUsername($name))
        {
            throw new InvalidArgumentException("Username is invalid");
        }

        return new self($name, $uuid);
    }
}