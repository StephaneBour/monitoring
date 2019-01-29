<?php

namespace Tests\Connections;

use App\Connections\Elasticsearch;
use App\Helpers\IndexHelper;
use Cviebrock\LaravelElasticsearch\Facade as ElasticsearchFacade;
use Tests\Fixtures\FTP;
use Tests\TestCase;

class FTPTest extends TestCase
{
    /**
     * @var Elasticsearch
     */
    public $connection;

    private $_app;

    public function setUp()
    {
        $this->_app = $this->createApplication();

        ElasticsearchFacade::index(['index' => IndexHelper::generateMonitoringIndex(), 'type' => 'doc', 'id' => 'test_ftp', 'body' => FTP::ftps()]);

        $this->connection = new \App\Connections\Ftp(FTP::ftps());
    }

    public function testLaunch()
    {
        $this->assertTrue($this->connection->launch());
    }
}
