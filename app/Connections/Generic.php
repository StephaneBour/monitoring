<?php

namespace App\Connections;

use App\Helpers\IndexHelper;
use App\Helpers\MonitoringHelper;
use App\Interfaces\Connection;
use Carbon\Carbon;

class Generic implements Connection
{
    protected $_config;

    protected $_query;

    protected $_requiredKeys = [];

    protected $_result;

    protected $_uuid;

    /**
     * @param array|null $config
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function checkConfig(array $config = null): bool
    {
        $requiredKeys = MonitoringHelper::convertMultidimensionalKeysToUnique($this->_requiredKeys);

        if (! empty($config)) {
            $this->_config = $config;
        }

        if (empty($this->_config)) {
            throw new \Exception('Your ' . self::class . ' config is empty');
        }

        $intersect = array_intersect($requiredKeys, MonitoringHelper::convertMultidimensionalKeysToUnique($this->_config));

        if (count(array_diff_assoc($requiredKeys, $intersect)) !== 0) {
            throw new \Exception('Your ' . self::class . ' config is invalid');
        }

        $this->_uuid = $this->_config['uuid'] . '_' . uniqid();

        return true;
    }

    public function checkThrottle(): bool
    {
    }

    public function condition(): bool
    {
    }

    public function exec()
    {
    }

    public function generateQuery(): array
    {
    }

    public function launch(): bool
    {
    }

    public function updateCheckStatus(string $status, $result = null)
    {
        $query = [
            'index' => IndexHelper::generateResultIndex(),
            'type' => 'doc',
            'id' => $this->_uuid,
            'body' => [
                'monitoring_id' => $this->_config['uuid'],
                'status' => $status,
                'raw' => json_encode($this->_config),
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'result' => $result,
            ],
        ];

        app('elasticsearch')->index($query);
        app('elasticsearch')->indices()->refresh(['index' => IndexHelper::generateResultIndex()]); // Force to refresh index

        // Update general status / launch alert
        if (in_array($status, [self::STATUS_FAIL, self::STATUS_OK])) {
            $actually = app('elasticsearch')->get([
                'index' => IndexHelper::generateMonitoringIndex(),
                'type' => 'doc',
                'id' => $this->_config['uuid'],
            ]);

            if (! empty($actually['_source']['status']) && $actually['_source']['status'] != $status) {
                // Status change, launch alerts
            }
            app('elasticsearch')->update([
                'index' => IndexHelper::generateMonitoringIndex(),
                'type' => 'doc',
                'id' => $this->_config['uuid'],
                'body' => [
                    'doc' => [
                        'status' => $status,
                    ],
                ],
            ]);

            app('elasticsearch')->indices()->refresh(['index' => IndexHelper::generateMonitoringIndex()]);
        }
    }
}
