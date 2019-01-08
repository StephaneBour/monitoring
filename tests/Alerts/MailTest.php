<?php

namespace Tests\Connections;

use App\Actions\Email;
use App\Interfaces\Connection;
use Tests\TestCase;

class MailTest extends TestCase
{
    private $_app;

    public function setUp()
    {
        $this->_app = $this->createApplication();
    }

    public function testAlert()
    {
        $config = [
            'uuid' => 'Tests',
            'to' => 'test@gmail.com',
            'subject' => 'Test alert by mail',
            'status' => Connection::STATUS_FAIL,
            'content' => 'C\'EST TOUT CASSÉ',
        ];

        (new Email($config))->send();

        $emails = app()->make('swift.transport')->driver()->messages();
        $this->assertCount(1, $emails);
        $this->assertEquals(['test@gmail.com'], array_keys($emails[0]->getTo()));
    }
}
