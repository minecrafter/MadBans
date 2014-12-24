<?php

namespace MadBans\Data;

class Server
{
    private $name;

    private function __construct($name)
    {
        $this->name = $name;
    }

    public static function create($name)
    {
        return new self($name);
    }

    public static function matchAll()
    {
        return new self("GLOBAL");
    }

    public function isGlobal()
    {
        return $this->name === "GLOBAL";
    }

    /**
     * Gets the name of this server.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    function __toString()
    {
        return $this->isGlobal() ? "all servers" : $this->name;
    }

}