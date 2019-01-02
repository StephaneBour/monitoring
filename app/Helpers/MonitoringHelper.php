<?php

namespace App\Helpers;

class MonitoringHelper
{
    /**
     * @param array       $array
     * @param string|null $prefix
     * @param array       $return
     *
     * @return array
     */
    public static function convertMultidimensionalKeysToUnique(array $array, string $prefix = null, array &$return = [])
    {
        foreach ($array as $index => $values) {
            if (! is_array($array[$index])) {
                if (is_int($index) && is_string($values)) {
                    $return[] = $prefix . $values;
                } else {
                    $return[] = $prefix . $index;
                }
            } else {
                $return[] = $prefix . $index;
                self::convertMultidimensionalKeysToUnique($array[$index], $prefix . $index . '.', $return);
            }
        }

        return $return;
    }
}
