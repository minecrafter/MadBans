<?php

namespace MadBans\Repositories\Profile;

use MadBans\Repositories\ProfileRepository;

class ChainingProfileRepository implements ProfileRepository
{
    private $repositories;

    /**
     * Creates a new chaining profile repository.
     *
     * @param array $repositories
     */
    function __construct($repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Resolves a player using a UUID.
     *
     * @param $uuid string
     * @return \MadBans\Data\Player
     */
    public function byUuid($uuid)
    {
        foreach ($this->repositories as $repository)
        {
            $user = $repository->byUuid($uuid);

            if ($user)
                return $user;
        }

        return FALSE;
    }

    /**
     * Resolves a player using a username.
     *
     * @param $username string
     * @return \MadBans\Data\Player
     */
    public function byUsername($username)
    {
        foreach ($this->repositories as $repository)
        {
            $user = $repository->byUsername($username);

            if ($user)
                return $user;
        }

        return NULL;
    }
}