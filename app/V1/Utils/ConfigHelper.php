<?php

namespace App\V1\Utils;

use App\V1\Configuration;

class ConfigHelper
{
    public static function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return config('cheeeh');
        }
        if (is_array($key)) {
            $keyTemp = [];
            foreach ($key as $k => $v) {
                $keyTemp['cheeeh.' . $k] = $v;
            }
            return config($keyTemp);
        }
        return config('cheeeh.' . $key, $default);
    }

    public static function getAppName()
    {
        return config('app.name');
    }

    public static function getAppUrl()
    {
        return config('app.url');
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
}
