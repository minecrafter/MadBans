<?php

namespace MadBans\Repositories;

interface PlayerLookupRepository
{
    /**
     * Attempts a lookup of possible players.
     *
     * @param string $term
     * @return array
     */
    public function search($term);
}