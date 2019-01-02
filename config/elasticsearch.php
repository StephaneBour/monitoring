<?php

return [

    /*
     * You can specify one of several different connections when building an
     * Elasticsearch client.
     *
     * Here you may specify which of the connections below you wish to use
     * as your default connection when building an client. Of course you may
     * use create several clients at once, each with different configurations.
     */

    'defaultConnection' => env('ELASTICSEARCH_DEFAULT_CONNECTION', 'default'),

    /*
     * These are the connection parameters used when building a client.
     */

    'index' => [
        'name' => env('ELASTICSEARCH_INDEX_NAME', 'monitoring'),
        'number_of_shards' => env('ELASTICSEARCH_INDEX_NB_SHARDS', 5),
        'number_of_replicas' => env('ELASTICSEARCH_INDEX_NB_REPLICAS', 3),
        'refresh_interval' => env('ELASTICSEARCH_INDEX_REFRESH_INTERVAL', -1),
        'results' => [
            'prefix' => env('ELASTICSEARCH_INDEX_RESULTS_PREFIX', 'results'),
            'period' => env('ELASTICSEARCH_INDEX_RESULTS_PERIOD', 'monthly'), // Create index daily, monthly ?
            'separator' => env('ELASTICSEARCH_INDEX_RESULTS_SEPARATOR', '.'),
            'number_of_shards' => env('ELASTICSEARCH_INDEX_RESULTS_NB_SHARDS', 15),
            'number_of_replicas' => env('ELASTICSEARCH_INDEX_RESULTS_NB_REPLICAS', 3),
            'refresh_interval' => env('ELASTICSEARCH_INDEX_RESULTS_REFRESH_INTERVAL', '60s'),
        ],
    ],
    'connections' => [

        'default' => [
            'hosts' => [
                [
                    'host' => env('ELASTICSEARCH_HOST', 'localhost'),
                    'port' => env('ELASTICSEARCH_PORT', 9200),
                    'scheme' => env('ELASTICSEARCH_SCHEME', null),
                    'user' => env('ELASTICSEARCH_USER', null),
                    'pass' => env('ELASTICSEARCH_PASS', null),

                    // If you are connecting to an Elasticsearch instance on AWS, you will need these values as well
                    'aws' => env('AWS_ELASTICSEARCH_ENABLED', false),
                    'aws_region' => env('AWS_REGION', ''),
                    'aws_key' => env('AWS_ACCESS_KEY_ID', ''),
                    'aws_secret' => env('AWS_SECRET_ACCESS_KEY', ''),
                ],
            ],

            'sslVerification' => null,
            'logging' => false,
            'logPath' => storage_path('logs/elasticsearch.log'),
            'logLevel' => Monolog\Logger::INFO,
            'retries' => null,
            'sniffOnStart' => false,
            'httpHandler' => null,
            'connectionPool' => null,
            'connectionSelector' => null,
            'serializer' => null,
            'connectionFactory' => null,
            'endpoint' => null,
            'namespaces' => [],
        ],
        'tests' => [
            'hosts' => [
                [
                    'host' => env('ELASTICSEARCH_HOST', 'elasticsearch'),
                    'port' => env('ELASTICSEARCH_PORT', 9800),
                ],
            ],

            'sslVerification' => null,
            'logging' => false,
            'logPath' => storage_path('logs/elasticsearch_tests.log'),
            'logLevel' => Monolog\Logger::DEBUG,
            'retries' => null,
            'sniffOnStart' => false,
            'httpHandler' => null,
            'connectionPool' => null,
            'connectionSelector' => null,
            'serializer' => null,
            'connectionFactory' => null,
            'endpoint' => null,
            'namespaces' => [],
        ],
    ],
];
