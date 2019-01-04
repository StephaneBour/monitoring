<?php

namespace App\Conditions;

class Parse
{
    public static function exists(string $content, string $value)
    {
        return (strpos($content, $value) === false) ? false : true;
    }

    public static function exists_insensitive(string $content, string $value)
    {
        return (stripos($content, $value) === false) ? false : true;
    }

    public static function not_exists(string $content, string $value)
    {
        return (strpos($content, $value) === false) ? true : false;
    }

    public static function not_exists_insensitive(string $content, string $value)
    {
        return (stripos($content, $value) === false) ? true : false;
    }
}
