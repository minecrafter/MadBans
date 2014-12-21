<?php
namespace MadBans\Utilities;

use Carbon\Carbon;

class PdoHelper
{
    const FORMAT = 'Y-n-j G:i:s';

    public static function dateFromPdo($pdo_time)
    {
        return Carbon::createFromFormat(self::FORMAT, $pdo_time);
    }

    public static function dateToPdo(Carbon $date)
    {
        return $date->format(self::FORMAT);
    }
}