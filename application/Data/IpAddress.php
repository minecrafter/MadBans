<?php

namespace MadBans\Data;

use InvalidArgumentException;

class IpAddress
{
    private $ip;

    private function __construct($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Returns an object describing the request's IPv4 address.
     *
     * @return IpAddress
     */
    public static function current()
    {
        return self::fromIpv4($_SERVER['REMOTE_ADDRESS']);
    }

    /**
     * Returns an object with the specified IPv4 address.
     *
     * @param $address
     * @return IpAddress
     */
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

    /**
     * Returns the IP address.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}