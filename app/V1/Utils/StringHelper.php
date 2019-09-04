<?php

namespace App\V1\Utils;

class StringHelper
{
    public static function fill($text, $length, $char)
    {
        $textLength = mb_strlen($text);
        return $textLength >= $length ?
            $text : str_repeat($char, $length - $textLength) . $text;
    }

    public static function fillFollow($text, $followText, $char)
    {
        return static::fill($text, mb_strlen($followText), $char);
    }
}
