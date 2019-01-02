<?php

namespace App\Console\Commands\Monitoring;

use Cviebrock\LaravelElasticsearch\Facade as Elasticsearch;
use Illuminate\Console\Command;

class CheckCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch all checks';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:check';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $enableds = [];

        $results = Elasticsearch::scroll([
            'index' => config('elasticsearch.index.name'),
            'body' => [
                'query' => [
                    'match' => [
                        'enabled' => true,
                    ],
                ],
            ],
        ]);

        $this->output->writeln(json_encode($results));
    }
}
