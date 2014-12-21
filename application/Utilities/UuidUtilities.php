<?php

namespace MadBans\Utilities;

use Rhumsaa\Uuid\Uuid;

class UuidUtilities
{
    /**
     * Regex for a valid Minecraft username.
     */
    const VALID_MINECRAFT_USERNAME = "([A-Za-z_0-9]){1,16}";

    /**
     * Regex for a valid Mojang UUID.
     */
    const VALID_MOJANG_UUID = "([a-f0-9]){32}";

    /**
     * Regex for a valid Java UUID.
     */
    const VALID_JAVA_UUID = "[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}";

    /**
     * Generates a offline-mode player UUID.
     *
     * @param $username string
     * @return string
     */
    public static function constructOfflinePlayerUuid($username)
    {
        $data = hex2bin(md5("OfflinePlayer:" . $username));
        $data[6] = chr(ord($data[6]) & 0x0f | 0x30); // version 3 UUID
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // IETF variant
        return self::createJavaUuid(bin2hex($data));
    }

    /**
     * Verifies if the specified username could be a valid Minecraft username.
     *
     * @param string $username
     * @return bool
     */
    public static function validMinecraftUsername($username)
    {
        return preg_match_all('/' . self::VALID_MINECRAFT_USERNAME . '/', $username) === 1;
    }

    /**
     * Translates a Mojang UUID into a Java UUID.
     *
     * @param string $mojang_uuid
     * @return string
     */
    public static function createJavaUuid($mojang_uuid)
    {
        if (!preg_match_all('/' . static::VALID_MOJANG_UUID . '/', $mojang_uuid))
        {
            throw new \InvalidArgumentException($mojang_uuid . " is not a valid Mojang UUID");
        }

        $components = array(
            substr($mojang_uuid, 0, 8),
            substr($mojang_uuid, 8, 4),
            substr($mojang_uuid, 12, 4),
            substr($mojang_uuid, 16, 4),
            substr($mojang_uuid, 20),
        );

        return implode('-', $components);
    }

    /**
     * Translate a Java UUID into a Mojang UUID.
     *
     * @param \Rhumsaa\Uuid\Uuid|string $uuid
     * @return string
     */
    public static function createMojangUuid($uuid)
    {
        if ($uuid instanceof Uuid)
        {
            return str_replace('-', '', $uuid->toString());
        }

        return str_replace('-', '', $uuid);
    }
}