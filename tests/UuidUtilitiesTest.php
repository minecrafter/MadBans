<?php

use Rhumsaa\Uuid\Uuid;
use MadBans\Utilities\UuidUtilities;

class UuidUtilitiesTest extends PHPUnit_Framework_TestCase
{
    public function testOfflineUuidValidity()
    {
        $phpUuid = UuidUtilities::constructOfflinePlayerUuid("tuxed");
        $this->assertEquals(Uuid::fromString("708f6260-183d-3912-bbde-5e279a5e739a"), $phpUuid);
    }

    public function testUsernameValidity()
    {
        $this->assertTrue(UuidUtilities::validMinecraftUsername('tuxed'));
        $this->assertTrue(UuidUtilities::validMinecraftUsername('Cave_Johnson'));
        $this->assertTrue(UuidUtilities::validMinecraftUsername('Sammy_Blue5'));
        $this->assertFalse(UuidUtilities::validMinecraftUsername('blatantly_long_username'));
        $this->assertFalse(UuidUtilities::validMinecraftUsername('No|match'));
    }
}
