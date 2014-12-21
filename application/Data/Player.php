<?php

namespace MadBans\Data;

use MadBans\Utilities\UuidUtilities;
use Rhumsaa\Uuid\Uuid;
use InvalidArgumentException;

class Player
{
    private $name;
    private $uuid;

    private function __construct($name, Uuid $uuid)
    {
        $this->name = $name;
        $this->uuid = $uuid;
    }

    /**
     * Creates a fresh player object from the specified name and UUID.
     *
     * @param string $name
     * @param \Rhumsaa\Uuid\Uuid|string $uuid
     * @return Player
     */
    public static function fromNameAndUuid($name, $uuid)
    {
        if (!$uuid instanceof Uuid && !Uuid::isValid($uuid))
        {
            throw new InvalidArgumentException("UUID is not valid");
        }

        if (!UuidUtilities::validMinecraftUsername($name))
        {
            throw new InvalidArgumentException("Username is invalid");
        }

        return new self($name, $uuid instanceof Uuid ? $uuid : Uuid::fromString($uuid));
    }

    /**
     * Returns this player's username.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the user's UUID.
     *
     * @return \Rhumsaa\Uuid\Uuid
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}