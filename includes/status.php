<?php

function isUserOnline($last_seen)
{
    if (empty($last_seen)) {
        return false;
    }

    return strtotime($last_seen) >= strtotime('-2 minutes');
}

?>