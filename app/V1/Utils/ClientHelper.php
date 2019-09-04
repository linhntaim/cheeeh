<?php

namespace App\V1\Utils;

use Jenssegers\Agent\Agent;

class ClientHelper
{
    public static function information()
    {
        $agent = new Agent();
        $info = [];
        if ($agent->isMobile() || $agent->isTablet()) {
            $info[] = $agent->device();
        }
        $info[] = $agent->platform() . ' (' . $agent->version($agent->platform()) . ')';
        $info[] = $agent->browser() . ' (' . $agent->version($agent->browser()) . ')';
        $info[] = request()->header('User-Agent');
        return implode(', ', $info);
    }
}
