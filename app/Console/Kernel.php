<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands
        = [
            //
        ];

    private $_checks = [];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->_getChecks();
        foreach ($this->_checks as $check) {
            $command = $schedule->command('monitoring:check ' . $check['uuid'])->everyMinute();
            if (! empty($check['one_server']) && $check['one_server']) {
                $command = $command->runInBackground();
            }
            if (! empty($check['timeout']) && $check['timeout'] > 0) {
                $command = $command->withoutOverlapping($check['timeout']);
            } else {
                $command = $command->withoutOverlapping(60);
            }
        }
    }

    /**
     * @param array $results
     *
     * @return $this
     */
    private function _extractChecks(array $results)
    {
        if (! empty($results['hits']['hits'])) {
            foreach ($results['hits']['hits'] as $hit) {
                if ($hit['_source']['enabled'] === true || $this->input->getOption('all')) {
                    $this->_checks[] = $hit['_source'];
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function _getChecks()
    {
        $results = app('elasticsearch')->search([
            'scroll' => '1m',
            'size' => 1000,
            'index' => config('elasticsearch.index.name'),
            'type' => 'doc',
            'body' => [
                'query' => [
                    'match' => [
                        'enabled' => true,
                    ],
                ],
            ],
        ]);

        $this->_extractChecks($results);
        while (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
            $scroll_id = $results['_scroll_id'];
            // Execute a Scroll request and repeat
            $results = app('elasticsearch')->scroll([
                    'scroll_id' => $scroll_id,  //...using our previously obtained _scroll_id
                    'scroll' => '30s',           // and the same timeout window
                ]
            );
            $this->_extractChecks($results);
        }

        return $this;
    }
}
