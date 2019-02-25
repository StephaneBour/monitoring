<?php

namespace App\Console\Commands\Templates;

use App\Helpers\IndexHelper;
use Elasticsearch\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Question\Question;

class CreateCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create monitoring templates';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:create {--force}';

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
        $this->_monitoringIndex();
        $this->_monitoringResultsIndex();
    }

    /**
     * @param string $index
     */
    private function _checkIfExist(string $index): void
    {
        $this->output->writeln('Check if ' . $index . ' exists');
        if ($this->_elastic->indices()->exists(['index' => $index]) === true && $this->option('force') == false) {
            $anwser = $this->output->askQuestion(new Question('Index `' . $index . '` exists. Want to reindex with the new mapping ? Y/n', 'n'));
            if ($anwser == 'Y') {
                $new_index = $index . '_' . date('Ymdhis');
                $this->output->writeln('Create index ' . $new_index);
                $this->_elastic->indices()->create(['index' => $new_index]);
                $this->output->writeln('Reindex ' . $index . ' in ' . $new_index);
                $this->_elastic->reindex(['body' => ['source' => ['index' => $index], 'dest' => ['index' => $new_index]]]);
                if ($this->_elastic->indices()->existsAlias(['name' => $index])) {
                    $alias = $this->_elastic->indices()->getAlias(['name' => $index]);
                    if (count($alias) > 0) {
                        $alias = key($alias);
                        $this->output->writeln('Delete alias ' . $alias . ' on ' . $index);
                        $this->_elastic->indices()->deleteAlias(['index' => $alias, 'name' => $index]);
                    }
                }
                if ($this->_elastic->indices()->exists(['index' => $index]) === true) {
                    $this->output->writeln('Delete ' . $index);
                    $this->_elastic->indices()->delete(['index' => $index]);
                }
                $this->output->writeln('Create alias from ' . $new_index . ' to ' . $index);
                $this->_elastic->indices()->putAlias([
                    'index' => $new_index,
                    'name' => $index,
                ]);
            }
        }
    }

    /**
     * @param string $index
     */
    private function _checkIfTemplateExist(string $index): void
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
        $this->_checkIfTemplateExist(config('elasticsearch.index.name'));

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
                            'last_check' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss',
                            ],
                            'enabled' => [
                                'type' => 'boolean',
                            ],
                            'one_server' => [
                                'type' => 'boolean',
                            ],
                            'timeout' => [
                                'type' => 'long',
                            ],
                            'input' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'conditions' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'throttle_period' => [
                                'type' => 'keyword',
                            ],
                            'times' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'uuid' => [
                                'type' => 'keyword',
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

        $this->output->success(config('elasticsearch.index.name') . ' template updated');

        $this->_checkIfExist(IndexHelper::generateMonitoringIndex());

        return $this;
    }

    /**
     * @return $this
     */
    private function _monitoringResultsIndex()
    {
        $this->_checkIfTemplateExist(config('elasticsearch.index.name') . '_' . config('elasticsearch.index.results.prefix'));

        $this->_elastic->indices()->putTemplate([
            'name' => config('elasticsearch.index.name') . '_' . config('elasticsearch.index.results.prefix'),
            'body' => [
                'order' => 0,
                'index_patterns' => [
                    config('elasticsearch.index.name') . '_' . config('elasticsearch.index.results.prefix') . '-*',
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
                            'status' => [
                                'type' => 'keyword',
                            ],
                            'result' => [
                                'type' => 'object',
                                'enabled' => false,
                                'dynamic' => true,
                            ],
                            'date' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd HH:mm:ss',
                            ],
                            'raw' => [
                                'type' => 'keyword',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->output->success(config('elasticsearch.index.name') . '_' . config('elasticsearch.index.results.prefix') . ' template updated');

        $this->_checkIfExist(IndexHelper::generateResultIndex());

        return $this;
    }
}
