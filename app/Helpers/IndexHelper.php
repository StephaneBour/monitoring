<?php

namespace App\Helpers;

class IndexHelper
{
    /**
     * @param string $index
     * @param string $frequence
     * @param string $separator
     *
     * @return string
     */
    public static function generateIndex(string $index, string $frequence = 'monthly', string $separator = '.')
    {
        $name = $index . '-';

        switch ($frequence) {
            case 'daily':
                return $name . date('Y' . $separator . 'm' . $separator . 'd');
            case 'annualy':
                return $name . date('Y');
            default:
                return $name . date('Y' . $separator . 'm');
        }
    }

    /**
     * returns the name of the results index.
     *
     * @return string
     */
    public static function generateResultIndex()
    {
        $name = config('elasticsearch.index.name').'_' . config('elasticsearch.index.results.prefix');

        return self::generateIndex($name, config('elasticsearch.index.results.period'), config('elasticsearch.index.results.separator'));
    }
}
