<?php

namespace App\Helpers;

use App\Exceptions\Fail;

class SSLHelper
{
    /**
     * @param string $url
     *
     * @throws Fail
     *
     * @return bool
     */
    public static function stillValid(string $url): bool
    {
        $certinfo = self::get($url);
        if (empty($certinfo['validFrom_time_t'])) {
            throw new Fail('No valid certificate date for the domain ' . $url);
        } else {
            return time() < $certinfo['validFrom_time_t'];
        }
    }

    /**
     * @param string $url
     *
     * @throws Fail
     *
     * @return int
     */
    public static function validFrom(string $url): int
    {
        $certinfo = self::get($url);
        if (empty($certinfo['validFrom_time_t'])) {
            throw new Fail('No valid certificate date for the domain ' . $url);
        } else {
            return intval($certinfo['validFrom_time_t']);
        }
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private static function get(string $url): array
    {
        $orignal_parse = parse_url($url, PHP_URL_HOST);
        $get = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
        $read = stream_socket_client('ssl://'.$orignal_parse.':443', $errno, $errstr,
            30, STREAM_CLIENT_CONNECT, $get);
        $cert = stream_context_get_params($read);
        $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

        return $certinfo;
    }
}
