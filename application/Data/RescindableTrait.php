<?php
/**
 * Created by PhpStorm.
 * User: tux
 * Date: 12/20/14
 * Time: 3:00 PM
 */

namespace MadBans\Data;

trait RescindableTrait
{
    public $rescinded;
    public $rescind_date;
    public $rescind_reason;
}