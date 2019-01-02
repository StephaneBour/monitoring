<?php

namespace App\Conditions;

class Count
{
    /**
     * Equal.
     *
     * @param int $value
     * @param int $compare
     *
     * @return bool
     */
    public static function equal(int $value, int $compare): bool
    {
        return $value === $compare;
    }

    /**
     * Greater than.
     *
     * @param int $value
     * @param int $compare
     *
     * @return bool
     */
    public static function gt(int $value, int $compare): bool
    {
        return $value > $compare;
    }

    /**
     * Greater than or equal.
     *
     * @param int $value
     * @param int $compare
     *
     * @return bool
     */
    public static function gte(int $value, int $compare): bool
    {
        return $value >= $compare;
    }

    /**
     * Lower than.
     *
     * @param int $value
     * @param int $compare
     *
     * @return bool
     */
    public static function lt(int $value, int $compare): bool
    {
        return $value < $compare;
    }

    /**
     * Lower than or equal.
     *
     * @param int $value
     * @param int $compare
     *
     * @return bool
     */
    public static function lte(int $value, int $compare): bool
    {
        return $value <= $compare;
    }

    /**
     * Range.
     *
     * @param int $value
     * @param int $min
     * @param int $max
     *
     * @return bool
     */
    public static function range(int $value, array $range): bool
    {
        return $value >= $range['min'] && $value <= $range['max'];
    }
}
