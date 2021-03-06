<?php

namespace App\Connections;

use App\Exceptions\Fail;
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

    /**
     * Throttle process.
     *
     * @return bool
     */
    public function checkThrottle(): bool
    {
        if (! app('elasticsearch')->indices()->exists(['index' => IndexHelper::generateResultIndex()])) {
            app('elasticsearch')->indices()->create(['index' => IndexHelper::generateResultIndex()]);
        }
        // Last ?
        $query = [
            'index' => IndexHelper::generateResultIndex(),
            'type' => 'doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'range' => [
                                    'date' => [
                                        'from' => 'now-' . $this->_config['throttle_period'],
                                    ],
                                ],
                            ], [
                                'match' => [
                                    'monitoring_id' => $this->_config['uuid'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (intval(app('elasticsearch')->count($query)['count']) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if the return fulfill the conditions.
     *
     * @return bool
     */
    public function conditions(): bool
    {
        if (empty($this->_config['conditions'])) {
            return false;
        }
        $status = true;

        foreach ($this->_config['conditions'] as $mode => $methods) {
            $class = '\App\Conditions\\' . ucfirst(strtolower($mode));
            foreach ($methods as $method => $value) {
                if (is_string($method)) {
                    if ($class::$method($this->_result, $value) === false) {
                        $status = false;
                    }
                } else {
                    if ($class::$value($this->_result) === false) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }

    public function exec()
    {
    }

    public function generateQuery(): array
    {
    }

    /**
     * Launch a complete test.
     *
     * @return bool
     */
    public function launch(): bool
    {
        $errors = [];
        if ($this->_config['enabled']) {
            if ($this->checkThrottle()) {
                $this->updateCheckStatus(self::STATUS_WAITING);
                try {
                    $this->exec();

                    if ($this->conditions()) {
                        $this->updateCheckStatus(self::STATUS_OK, $this->_result, $errors);

                        return true;
                    } else {
                        $this->updateCheckStatus(self::STATUS_FAIL, $this->_result, $errors);

                        return false;
                    }
                } catch (Fail $fail) {
                    $errors[] = $fail->getMessage();
                    $this->updateCheckStatus(self::STATUS_FAIL, $this->_result, $errors);

                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $status
     */
    public function updateCheckStatus(string $status, $result = null, array $errors = [])
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
                // Launch alerts
                $this->_launchAlerts($errors);
            }
            app('elasticsearch')->update([
                'index' => IndexHelper::generateMonitoringIndex(),
                'type' => 'doc',
                'id' => $this->_config['uuid'],
                'body' => [
                    'doc' => [
                        'status' => $status,
                        'last_check' => date('Y-m-d H:i:s'),
                    ],
                ],
            ]);

            app('elasticsearch')->indices()->refresh(['index' => IndexHelper::generateMonitoringIndex()]);
        }
    }

    /**
     * @param array $errors
     */
    private function _launchAlerts(array $errors = []): void
    {
        if (! empty($errors)) {
            $content = implode(', ', $errors);
        }

        if (! empty($this->_config['actions'])) {
            foreach ($this->_config['actions'] as $action => $config) {
                logger('Launch alert for ' . $this->_config['uuid'] . ' : ' . $action . ' : ' . json_encode($config));
                $class = '\App\Actions\\' . ucfirst($action);
                if (isset($content)) {
                    $config['content'] = $content;
                }

                $action = new $class($config);
                $action->send();
            }
        }
    }
}
