<?php

namespace Tests\Connections;

use App\Actions\Slack;
use App\Interfaces\Connection;
use Tests\TestCase;

class SlackTest extends TestCase
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
            'status' => Connection::STATUS_FAIL,
            'content' => 'C\'EST TOUT CASSÉ',
        ];

        (new Slack($config))->send();
    }
}
