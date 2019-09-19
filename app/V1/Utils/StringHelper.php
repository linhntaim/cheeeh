<?php

namespace App\V1\Utils;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StringHelper
{
    public static function repeat($char, $time)
    {
        if (is_callable($char)) {
            $r = '';
            while (--$time >= 0) {
                $r .= $char();
            }
            return $r;
        }
        return str_repeat($char, $time);
    }

    public static function fill($text, $length, $char, &$filled = 0)
    {
        $textLength = mb_strlen($text);
        if ($textLength >= $length) return $text;

        $filled = $length - $textLength;
        return static::repeat($char, $length - $textLength) . $text;
    }

    public static function fillFollow($text, $followText, $char, &$filled = 0)
    {
        return static::fill($text, mb_strlen($followText), $char, $filled);
    }

    public static function fillAfter($text, $length, $char, &$filled = 0)
    {
        $textLength = mb_strlen($text);
        if ($textLength >= $length) return $text;

        $filled = $length - $textLength;
        return $text . static::repeat($char, $length - $textLength);
    }

    public static function fillAfterFollow($text, $followText, $char, &$filled = 0)
    {
        return static::fillAfter($text, mb_strlen($followText), $char, $filled);
    }

    public static function hashRandom($length = 32)
    {
        return static::hash(Str::random($length));
    }

    public static function hash($text)
    {
        return Hash::make($text);
    }

    public static function uuid()
    {
        return Str::uuid()->toString();
    }
}
