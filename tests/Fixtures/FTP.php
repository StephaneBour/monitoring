<?php

namespace Tests\Fixtures;

class FTP
{
    public static function ftps()
    {
        return [
            'enabled' => true,
            'uuid' => 'test_ftp',
            'input' => [
                'connection' => 'ftp',
                'host' => 'ftp.localhost',
                'type' => 'fileExist',
                'mode' => 'ftps',
                'login' => 'root',
                'password' => 'toor',
                'directory' => '/secret',
                'filename' => 'all-datas-\Y\m\d.csv',
                'interval' => [
                    'time' => 'P2D',
                ],
            ],
            'throttle_period' => '5s',
            'actions' => [
                'email' => [
                    'to' => 'alerts@stephane.tech',
                    'subject' => 'Attention, problème avec le FTP',
                ],
            ],
        ];
    }
}
