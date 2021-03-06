<?php

namespace App\V1\Utils;

use App\V1\Configuration;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait ClassTrait
{
    protected static function __class()
    {
        return static::class;
    }

    protected static function __classBaseName()
    {
        return class_basename(static::class);
    }

    protected static function __snakyClassBaseName()
    {
        return Str::snake(static::__classBaseName());
    }

    protected static function __friendlyClassBaseName()
    {
        return Str::title(Str::snake(static::__classBaseName(), ' '));
    }

    protected static function __hasTransWithModule($name, $module, $locale = null, $fallback = true)
    {
        return Lang::has(static::__transPathWithModule($name, $module), $locale, $fallback);
    }

    protected static function __transWithModule($name, $module, $replace = [], $locale = null)
    {
        return trans(static::__transPathWithModule($name, $module), $replace, $locale);
    }

    protected static function __hasTransWithSpecificModule($name, $module, $locale = null, $fallback = true)
    {
        return Lang::has(static::__transPathWithModule($name, $module, true), $locale, $fallback);
    }

    protected static function __transWithSpecificModule($name, $module, $replace = [], $locale = null)
    {
        return trans(static::__transPathWithModule($name, $module, true), $replace, $locale);
    }

    protected static function __transPathWithModule($name, $module, $specific = false)
    {
        if ($specific) {
            return sprintf('%s.%s.%s', $module, static::__snakyClassBaseName(), $name);
        }

        $classNames = explode('\\', str_replace(Configuration::ROOT_NAMESPACE . '\\', '', static::class));
        foreach ($classNames as &$className) {
            $className = Str::snake($className);
        }
        return sprintf('%s.%s.%s', $module, implode('.', $classNames), $name);
    }

    protected static function __transErrorWithModule($error, $replace = [], $locale = null)
    {
        return static::__transWithModule($error, 'error', $replace, $locale);
    }

    protected static function __hasTransErrorWithModule($error, $locale = null, $fallback = true)
    {
        return static::__hasTransWithModule($error, 'error', $locale, $fallback);
    }

    protected static function __transErrorPathWithModule($error)
    {
        return static::__transPathWithModule($error, 'error');
    }

    protected static function __transError($error, $replace = [], $locale = null)
    {
        return trans(static::__transErrorPath($error), $replace, $locale);
    }

    protected static function __transErrorPath($error)
    {
        return 'error.' . $error;
    }
}
