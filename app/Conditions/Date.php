<?php

namespace App\Conditions;

class Date
{
    /**
     * Equal.
     *
     * @param \DateTime $value
     * @param \DateTime $compare
     *
     * @return bool
     */
    public static function equal(\DateTime $value, \DateTime $compare, bool $strict = true): bool
    {
        if ($strict !== true) {
            self::resetHours($value, $compare);
        }

        return $value->getTimestamp() == $compare->getTimestamp();
    }

    /**
     * Greater than.
     *
     * @param \DateTime $value
     * @param \DateTime $compare
     *
     * @return bool
     */
    public static function gt(\DateTime $value, \DateTime $compare, bool $strict = true): bool
    {
        if ($strict !== true) {
            self::resetHours($value, $compare);
        }

        return $value->getTimestamp() > $compare->getTimestamp();
    }

    /**
     * Greater than or equal.
     *
     * @param \DateTime $value
     * @param \DateTime $compare
     *
     * @return bool
     */
    public static function gte(\DateTime $value, \DateTime $compare, bool $strict = true): bool
    {
        if ($strict !== true) {
            self::resetHours($value, $compare);
        }

        return $value->getTimestamp() >= $compare->getTimestamp();
    }

    /**
     * Lower than.
     *
     * @param \DateTime $value
     * @param \DateTime $compare
     *
     * @return bool
     */
    public static function lt(\DateTime $value, \DateTime $compare, bool $strict = true): bool
    {
        if ($strict !== true) {
            self::resetHours($value, $compare);
        }

        return $value->getTimestamp() < $compare->getTimestamp();
    }

    /**
     * Lower than or equal.
     *
     * @param \DateTime $value
     * @param \DateTime $compare
     *
     * @return bool
     */
    public static function lte(\DateTime $value, \DateTime $compare, bool $strict = true): bool
    {
        if ($strict !== true) {
            self::resetHours($value, $compare);
        }

        return $value->getTimestamp() <= $compare->getTimestamp();
    }

    /**
     * Range.
     *
     * @param \DateTime $value
     * @param \DateTime $min
     * @param \DateTime $max
     *
     * @return bool
     */
    public static function range(\DateTime $value, array $range, bool $strict = true): bool
    {
        if ($strict !== true) {
            self::resetHours($value, $range['min'], $range['max']);
        }

        return $value->getTimestamp() >= $range['min']->getTimestamp() && $value->getTimestamp() <= $range['max']->getTimestamp();
    }

    /**
     * @param string    $method
     * @param \DateTime $value
     *
     * @return bool
     */
    public static function today(\DateTime $value, array $config): bool
    {
        $method = $config['method'];

        $strict = true;
        if (isset($config['strict'])) {
            $strict = $config['strict'];
        }

        return self::$method($value, new \DateTime(), $strict);
    }

    /**
     * Set hours, minutes and seconds to 0.
     *
     * @param \DateTime ...$dateTimes
     */
    private static function resetHours(\DateTime ...$dateTimes)
    {
        foreach ($dateTimes as $dateTime) {
            $dateTime->setTime(0, 0, 0);
        }
    }
}
