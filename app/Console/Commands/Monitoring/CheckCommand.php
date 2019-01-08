<?php

namespace App\Console\Commands\Monitoring;

use App\Helpers\IndexHelper;
use Illuminate\Console\Command;

class CheckCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch one check';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:check {uuid} {--force}';

    private $_config;

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
        $this->_getConfig();
        if ((! empty($this->_config['enabled']) && $this->_config['enabled']) || $this->input->getOption('force')) {
            $connection = '\App\Connections\\' . ucfirst($this->_config['input']['connection']);

            $check = new $connection($this->_config);
            $check->launch();
        }
    }

    private function _getConfig()
    {
        $this->_config = app('elasticsearch')->get([
            'index' => IndexHelper::generateMonitoringIndex(),
            'type' => 'doc',
            'id' => $this->input->getArgument('uuid'),
        ])['_source'];
    }
}
