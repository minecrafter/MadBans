<?php

namespace MadBans\Data;

use Carbon\Carbon;

trait ExpirableTrait
{
    public $expiry;

    public function expired()
    {
        return Carbon::now()->gt($this->expiry);
    }
}