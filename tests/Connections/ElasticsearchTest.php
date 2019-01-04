<?php

namespace Tests\Connections;

use App\Connections\Elasticsearch;
use App\Helpers\IndexHelper;
use Cviebrock\LaravelElasticsearch\Facade as ElasticsearchFacade;
use Illuminate\Support\Facades\Artisan;
use Tests\Fixtures\Attractions;
use Tests\TestCase;

class ElasticsearchTest extends TestCase
{
    /**
     * @var Elasticsearch
     */
    public $connection;

    private $_app;

    public function setUp()
    {
        $this->_app = $this->createApplication();

        if (env('ELASTICSEARCH_TESTS', false) === true) {
            // Template
            Artisan::call('templates:create', ['--force' => true]);

            // Fixture Attractions
            if (! ElasticsearchFacade::indices()->exists(['index' => IndexHelper::generateMonitoringIndex()])) {
                ElasticsearchFacade::indices()->create(['index' => IndexHelper::generateMonitoringIndex()]);
                ElasticsearchFacade::indices()->refresh(['index' => IndexHelper::generateMonitoringIndex()]);
            }
            ElasticsearchFacade::index(['index' => IndexHelper::generateMonitoringIndex(), 'type' => 'doc', 'id' => 'test_attractions', 'body' => Attractions::monitoring()]);

            if (! ElasticsearchFacade::indices()->exists(['index' => IndexHelper::generateIndex('attractions', 'monthly', '-')])) {
                ElasticsearchFacade::indices()->create(['index' => IndexHelper::generateIndex('attractions', 'monthly', '-')]);
            }
            ElasticsearchFacade::indices()->putTemplate(['name' => 'attractions', 'body' => Attractions::template()]);
            ElasticsearchFacade::bulk(Attractions::data(10));
        }

        $this->connection = new Elasticsearch(Attractions::monitoring());
    }

    public function testCondition()
    {
        $this->connection->exec();
        $this->assertTrue($this->connection->conditions());
    }

    public function testConfig()
    {
        try {
            $return = $this->connection->checkConfig();
            $this->assertTrue($return);
        } catch (\Exception $ex) {
            $this->assertEquals($ex->getMessage(), 'Your ' . Elasticsearch::class . ' config is invalid');
        }
    }

    public function testExec()
    {
        $this->assertGreaterThan(9, $this->connection->exec());
    }

    public function testGenerateQuery()
    {
        $query = $this->connection->generateQuery();
        $this->assertArrayHasKey('index', $query);
        $this->assertArrayHasKey('type', $query);
        $this->assertArrayHasKey('body', $query);
    }

    public function testLaunch()
    {
        $status = $this->connection->launch();
        // TODO : clarifier le test
        $this->assertTrue($status);
    }

    public function testThrottle()
    {
        $status = $this->connection->checkThrottle();
        $this->assertIsBool($status);
    }
}
