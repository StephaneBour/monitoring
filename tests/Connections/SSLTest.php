<?php

namespace Tests\Connections;

use App\Connections\Elasticsearch;
use App\Helpers\SSLHelper;
use Tests\TestCase;

class SSLTest extends TestCase
{
    /**
     * @var Elasticsearch
     */
    public $connection;

    private $_app;

    public function setUp()
    {
        $this->_app = $this->createApplication();
    }

    public function testCondition()
    {
        SSLHelper::stillValid('https://manager-welcome.welcome-media.fr');
    }
}
