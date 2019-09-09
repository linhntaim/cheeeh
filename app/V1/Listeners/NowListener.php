<?php

namespace App\V1\Listeners;

use App\V1\Utils\ClassTrait;

abstract class NowListener
{
    use ClassTrait;

    protected static function __transListener($name, $replace = [], $locale = null)
    {
        return static::__transWithSpecificModule($name, 'listener', $replace, $locale);
    }
}
