<?php

namespace Tests\Fixtures;

use App\Helpers\IndexHelper;
use Carbon\Carbon;

class Attractions
{
    public static function data($nb = 20)
    {
        $bulk = [];

        for ($i = 0; $i < $nb; $i++) {
            $bulk['body'][] = [
                'update' => [
                    '_index' => IndexHelper::generateIndex('attractions', 'monthly', '-'),
                    '_type' => 'doc',
                    '_id' => 'attractions_' . $i,
                ],
            ];

            $bulk['body'][] = ['doc' => [
                'disney_id' => 'P2ZA0' . rand(1, 9),
                'type' => 'Attraction',
                'fastPass' => rand(0, 1),
                'status' => 'Operating',
                'singleRider' => false,
                'waitTime' => 30,
                'id' => rand(1, 100),
                'date' => Carbon::now()->subSeconds(rand(120, 3600))->format('Y-m-d H:i:s'),
            ], 'doc_as_upsert' => true];
        }

        return $bulk;
    }

    public static function monitoring()
    {
        return [
            'enabled' => true,
            'uuid' => 'test_attractions',
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
            'conditions' => [
                'count' => [
                    'gt' => 0,
                    'lt' => 100,
                ],
                'parse' => [
                    'not_exists' => 'test',
                ],
            ],
            'throttle_period' => '5s',
            'actions' => [
                'slack' => [
                    'color' => '#F00',
                    'content' => 'Attention, plus de log des attractions disney',
                ],
                'email' => [
                    'to' => 'stephane.bour@gmail.com',
                    'subject' => 'Attention, plus de log des attractions disney',
                    'content' => 'Attention, plus de log des attractions disney',
                ],
            ],
        ];
    }

    public static function template()
    {
        return [
            'order' => 0,
            'index_patterns' => [
                'attractions-*',
            ],
            'settings' => [
                'index' => [
                    'number_of_shards' => '10',
                    'number_of_replicas' => '3',
                ],
            ],
            'mappings' => [
                'doc' => [
                    'properties' => [
                        'date' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ],
                        'disney_id' => [
                            'type' => 'keyword',
                        ],
                        'fastPass' => [
                            'type' => 'long',
                        ],
                        'id' => [
                            'type' => 'long',
                        ],
                        'singleRider' => [
                            'type' => 'boolean',
                        ],
                        'singleRinder' => [
                            'type' => 'long',
                        ],
                        'status' => [
                            'type' => 'keyword',
                        ],
                        'type' => [
                            'type' => 'keyword',
                        ],
                        'waitTime' => [
                            'type' => 'long',
                        ],
                    ],
                ],
            ],
        ];
    }
}
