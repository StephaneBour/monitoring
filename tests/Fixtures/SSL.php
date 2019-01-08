<?php

namespace Tests\Fixtures;

class SSL
{
    public static function monitoring()
    {
        return [
            'enabled' => true,
            'uuid' => 'test_ssl',
            'input' => [
                'connection' => 'ssl',
                'url' => 'https://www.google.fr',
                'type' => 'diff',
                'days' => 10,
                'mode' => 'stillValid',
            ],
            'throttle_period' => '5s',
            'actions' => [
                'email' => [
                    'to' => 'stephane.bour@gmail.com',
                    'subject' => 'Attention, le certificat SSL est dépassé',
                ],
            ],
        ];
    }
}
