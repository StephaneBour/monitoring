<?php

namespace Tests\Connections;

use App\Connections\Elasticsearch;
use App\Exceptions\Fail;
use App\Helpers\IndexHelper;
use App\Helpers\SSLHelper;
use Cviebrock\LaravelElasticsearch\Facade as ElasticsearchFacade;
use Tests\Fixtures\SSL;
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

        ElasticsearchFacade::index(['index' => IndexHelper::generateMonitoringIndex(), 'type' => 'doc', 'id' => 'test_ssl', 'body' => SSL::monitoring()]);

        $this->connection = new \App\Connections\Ssl(SSL::monitoring());
    }

    /**
     * @throws \App\Exceptions\Fail
     */
    public function testFail()
    {
        $this->expectException(Fail::class);
        SSLHelper::stillValid('https://test-ssl.stephane.tech');
    }

    public function testLaunch()
    {
        $this->assertTrue($this->connection->launch());
    }
}
