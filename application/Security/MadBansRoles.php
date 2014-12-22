<?php

namespace MadBans\Security;

class MadBansRoles
{
    /* View Roles */
    const VIEW_PLAYER_INFORMATION = "VIEW_PLAYER_INFORMATION";
    const VIEW_BAN_INFORMATION = "VIEW_BAN_INFORMATION";
    const VIEW_MUTE_INFORMATION = "VIEW_MUTE_INFORMATION";
    const VIEW_COMMENT_INFORMATION = "VIEW_COMMENT_INFORMATION";

    /* Add Roles */
    const ADD_BAN = "ADD_BAN";
    const ADD_MUTE = "ADD_MUTE";
    const ADD_COMMENT = "ADD_COMMENT";

    /* Rescind Roles */
    const RESCIND_BAN = "RESCIND_BAN";
    const RESCIND_MUTE = "RESCIND_MUTE";
    const RESCIND_COMMENT = "RESCIND_COMMENT";

    /* Administration Roles */
    const ADMIN_ROLE = "ADMIN_ROLE";
    const SUPERUSER_ROLE = "SUPERUSER_ROLE";
}