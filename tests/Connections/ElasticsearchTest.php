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
        $this->connection = new Elasticsearch([
            'enabled' => true,
            'input' => [
                'connection' => 'elasticsearch',
                'index' => 'attractions',
                'type' => 'doc',
                'frequence' => 'monthly',
                'separator' => '-',
                'mode' => 'count',
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'range' => [
                                    'date' => [
                                        'from' => 'now-2h',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'condition' => [
                'count' => [
                    'gt' => 31,
                ],
            ],
            'throttle_period' => 60,
            'actions' => [
                'slack' => [
                    'color' => '#F00',
                    'text' => 'Attention, plus de log des attractions disney',
                ],
                'email' => [
                    'to' => 'stephane.bour@gmail.com',
                    'subject' => 'Attention, plus de log des attractions disney',
                    'text' => 'Attention, plus de log des attractions disney',
                ],
            ],
        ]);

        if (env('ELASTICSEARCH_TESTS', false) === true) {
            // Template
            Artisan::call('templates:create', ['--force' => true]);

            // Fixture Attractions
            if (! ElasticsearchFacade::indices()->exists(['index' => IndexHelper::generateIndex('attractions', 'monthly', '-')])) {
                ElasticsearchFacade::indices()->create(['index' => IndexHelper::generateIndex('attractions', 'monthly', '-')]);
            }
            ElasticsearchFacade::indices()->putTemplate(['name' => 'attractions', 'body' => Attractions::template()]);
            ElasticsearchFacade::bulk(Attractions::data(10));
        }
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

    public function testGenerateQuery()
    {
        $query = $this->connection->generateQuery();
        $this->assertArrayHasKey('index', $query);
        $this->assertArrayHasKey('type', $query);
        $this->assertArrayHasKey('body', $query);
    }

    public function testExec()
    {
        $this->assertGreaterThan(9, $this->connection->exec());
    }

    public function testCondition()
    {
        $this->connection->exec();
        $this->assertTrue($this->connection->condition());
    }
}
