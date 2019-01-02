<?php

use Cviebrock\LaravelElasticsearch\Facade as Elasticsearch;
use Illuminate\Database\Seeder;

class MonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Elasticsearch::index([
                'body' => [
                    'enabled' => true,
                    'input' => [
                        'connection' => 'elasticsearch',
                        'index' => 'tracking-',
                        'type' => 'logs',
                        'frequence' => 'monthly',
                        'separator' => '.',
                        'mode' => 'count',
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    [
                                        'match' => [
                                            'host' => 'wtr01.prd.bha.amg.bds.systems',
                                        ],
                                    ], [
                                        'range' => [
                                            'time_iso8601' => [
                                                'from' => 'now-10m',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'condition' => [
                        'count' => [
                            'gt' => 0,
                        ],
                    ],
                    'throttle_period' => 60,
                    'actions' => [
                        'slack' => [
                            'color' => '#F00',
                            'text' => 'Attention, plus de log pour wtr01.prd.bha.amg.bds.systems',
                        ],
                        'email' => [
                            'to' => 'tech@welcoming.com',
                            'subject' => 'Attention, plus de log pour wtr01.prd.bha.amg.bds.systems',
                            'text' => 'Attention, plus de log pour wtr01.prd.bha.amg.bds.systems',
                        ],
                    ],
                ],
                'index' => config('elasticsearch.index.name'),
                'type' => 'doc',
                'id' => 'servers_tracking_log',
            ]
        );

        Elasticsearch::indices()->refresh(['index' => config('elasticsearch.index.name')]);
    }
}
