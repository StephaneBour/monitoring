<?php

namespace App\Conditions;

class Boolean
{
    /**
     * Is true.
     *
     * @param $value
     *
     * @return bool
     */
    public static function isFalse($value): bool
    {
        return boolval(intval($value)) === false;
    }

    /**
     * Is false.
     *
     * @param $value
     *
     * @return bool
     */
    public static function isTrue($value): bool
    {
        return boolval(intval($value)) === true;
    }
}
