<?php

namespace App\Console\Commands\Templates;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Question\Question;

class CreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:create {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create monitoring templates';

    /**
     * @var Client
     */
    private $_elastic;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_elastic = app('elasticsearch');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // TODO : export monitoring, delete index, import for recreate index
        $this->_monitoringIndex();
        $this->_monitoringResultsIndex();
    }

    /**
     * @param string $index
     */
    private function _checkIfExist(string $index): void
    {
        if ($this->_elastic->indices()->existsTemplate(['name' => $index]) === true && $this->option('force') == false) {
            $anwser = $this->output->askQuestion(new Question('Template `' . $index . '` exists. Want to replace ? Y/n', 'n'));
            if ($anwser !== 'Y') {
                exit(0);
            }
        }
    }

    /**
     * Create monitoring template (rules).
     *
     * @return $this
     */
    private function _monitoringIndex()
    {
        $this->_checkIfExist(config('elasticsearch.index.name'));

        // Update or create template

        $this->_elastic->indices()->putTemplate([
            'name' => config('elasticsearch.index.name'),
            'body' => [
                'order' => 0,
                'index_patterns' => [
                    config('elasticsearch.index.name'),
                ],
                'settings' => [
                    'index' => [
                        'number_of_shards' => config('elasticsearch.index.number_of_shards'),
                        'number_of_replicas' => config('elasticsearch.index.number_of_replicas'),
                        'refresh_interval' => config('elasticsearch.index.refresh_interval'), // Disable, and force refresh after edit / add
                    ],
                ],
                'mappings' => [
                    'doc' => [
                        'dynamic' => 'strict',
                        'properties' => [
                            'status' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'enabled' => [
                                'type' => 'boolean',
                            ],
                            'input' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'condition' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'throttle_period' => [
                                'type' => 'long',
                                'index' => false,
                                'doc_values' => false,
                            ],
                            'actions' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'metadata' => [
                                'type' => 'object',
                                'dynamic' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->output->success(config('elasticsearch.index.name') . ' updated');

        return $this;
    }

    /**
     * @return $this
     */
    private function _monitoringResultsIndex()
    {
        $this->_checkIfExist(config('elasticsearch.index.name').'_' . config('elasticsearch.index.results.prefix'));

        $this->_elastic->indices()->putTemplate([
            'name' => config('elasticsearch.index.name').'_' . config('elasticsearch.index.results.prefix'),
            'body' => [
                'order' => 0,
                'index_patterns' => [
                    config('elasticsearch.index.name').'_' . config('elasticsearch.index.results.prefix') . '-*',
                ],
                'settings' => [
                    'index' => [
                        'number_of_shards' => config('elasticsearch.index.results.number_of_shards'),
                        'number_of_replicas' => config('elasticsearch.index.results.number_of_replicas'),
                        'refresh_interval' => config('elasticsearch.index.results.refresh_interval'),
                    ],
                ],
                'mappings' => [
                    'doc' => [
                        'dynamic' => 'strict',
                        'properties' => [
                            'monitoring_id' => [
                                'type' => 'keyword',
                            ],
                            'result' => [
                                'type' => 'boolean',
                            ],
                            'raw' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->output->success(config('elasticsearch.index.name').'_' . config('elasticsearch.index.results.prefix') . ' updated');

        return $this;
    }
}
