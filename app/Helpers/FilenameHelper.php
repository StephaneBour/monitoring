<?php

namespace App\Helpers;

class FilenameHelper
{
    /**
     * Replace \ + character by date(character).
     *
     * @param string $name
     *
     * @return string
     */
    public static function dynamic(string $name, \DateTime $date = null): string
    {
        if ($date === null) {
            $date = new \DateTime();
        }

        return preg_replace_callback('|\\\\(\w{1})|', function ($matches) use ($date) {
            return $date->format($matches[1]);
        }, $name);
    }
}
