<?php

namespace App\V1\Utils;

class Helper
{
    public static function default($value, $default = null, callable $callback = null)
    {
        if (filled($value)) {
            return $callback ? $callback($value) : $value;
        }
        return $default;
    }

    public static function currentUserId($default = null)
    {
        $currentUser = request()->user();
        return empty($currentUser) ? $default : $currentUser->id;
    }
}
