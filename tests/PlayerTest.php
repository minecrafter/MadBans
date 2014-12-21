<?php

class PlayerTest extends PHPUnit_Framework_TestCase
{
    public function testValidCombination()
    {
        try
        {
            $uuid = \Rhumsaa\Uuid\Uuid::uuid4();
            \MadBans\Data\Player::fromNameAndUuid('Test', $uuid);
            \MadBans\Data\Player::fromNameAndUuid('Test', $uuid->toString());
        } catch (InvalidArgumentException $e)
        {
            $this->fail($e);
        }
    }

    public function testValidNameInvalidUuid()
    {
        try
        {
            \MadBans\Data\Player::fromNameAndUuid('Test', 'invalid');
        } catch (InvalidArgumentException $e)
        {
            return;
        }

        $this->fail("Username 'Test' and UUID 'failed' succeeded");
    }

    public function testInvalidNameValidUuid()
    {
        try
        {
            \MadBans\Data\Player::fromNameAndUuid(FALSE, \Rhumsaa\Uuid\Uuid::uuid4());
        } catch (InvalidArgumentException $e)
        {
            return;
        }

        $this->fail("False username and a valid UUID succeeded");
    }
}
