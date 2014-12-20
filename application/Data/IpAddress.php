<?php

namespace MadBans\Data;

use InvalidArgumentException;

class IpAddress
{
    public $ip;

    private function __construct($ip)
    {
        $this->ip = $ip;
    }

    public static function current()
    {
        return self::fromIpv4($_SERVER['REMOTE_ADDRESS']);
    }

    public static function fromIpv4($address)
    {
        // Ensure validity
        $components = explode('.', $address);

        if (count($components) !== 4)
        {
            throw new InvalidArgumentException("Invalid IPv4 address: " .  $address);
        }

        foreach ($components as $component)
        {
            if (!ctype_digit($component))
            {
                throw new InvalidArgumentException("Invalid IPv4 address: " .  $address);
            }

            $val = (int) $component;

            if ($val < 0 || $val > 255)
            {
                throw new InvalidArgumentException("Invalid IPv4 address: " .  $address);
            }
        }

        return new self($address);
    }
}