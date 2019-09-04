<?php

namespace App\V1\Utils;

use App\V1\Configuration;

class ConfigHelper
{
    public static function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return config('App');
        }
        if (is_array($key)) {
            $keyTemp = [];
            foreach ($key as $k => $v) {
                $keyTemp['App.' . $k] = $v;
            }
            return config($keyTemp);
        }
        return config('App.' . $key, $default);
    }

    public static function getAppName()
    {
        return static::get('app.name');
    }

    public static function getAppUrl()
    {
        return static::get('app.url');
    }

    public static function getNoReplyMail()
    {
        return static::get('emails.no_reply');
    }

    public static function getCurrentLocale()
    {
        return app()->getLocale();
    }

    public static function setCurrentLocale($locale)
    {
        app()->setLocale($locale);
    }

    public static function getLocaleCodes()
    {
        return static::get('locales');
    }

    public static function getCountries()
    {
        return static::get('countries');
    }

    public static function getCountryCodes()
    {
        return array_keys(static::getCountries());
    }

    public static function getCurrencies()
    {
        return static::get('currencies');
    }

    public static function getCurrencyCodes()
    {
        return array_keys(static::getCurrencies());
    }

    public static function getNumberFormats()
    {
        return static::get('number_formats');
    }

    public static function defaultAvatarUrl()
    {
        return static::get('app.default_avatar_url');
    }

    public static function getClockBlock($secondRange = Configuration::CLOCK_BLOCK_RANGE)
    {
        return floor(time() / $secondRange);
    }

    public static function getClockBlockKey($callback = '', $secondRange = Configuration::CLOCK_BLOCK_RANGE)
    {
        $blockKey = Configuration::CLOCK_BLOCK_KEYS[static::getClockBlock($secondRange) % count(Configuration::CLOCK_BLOCK_KEYS)];
        return !empty($callback) ? $callback($blockKey) : $blockKey;
    }

    public static function path($path = '')
    {
        return base_path($path ? 'App' . DIRECTORY_SEPARATOR . $path : 'App');
    }

    public static function appPath($path = '')
    {
        return static::path($path ? 'app' . DIRECTORY_SEPARATOR . $path : 'app');
    }
}
