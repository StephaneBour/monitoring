<?php

namespace App\Connections;

use App\Helpers\IndexHelper;

class Elasticsearch extends Generic
{
    /**
     * Elasticsearch constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
        $this->_requiredKeys = ['input' => ['index', 'type', 'frequence', 'mode', 'query'], 'conditions', 'throttle_period', 'actions', 'enabled', 'uuid'];
        $this->checkConfig();
        $this->generateQuery();
    }

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
                if ($class::$method($this->_result, $value) === false) {
                    $status = false;
                }
            }
        }

        return $status;
    }

    /**
     * @return int
     */
    public function exec()
    {
        switch ($this->_config['input']['mode']) {
            case 'count':
                $this->_result = intval(app('elasticsearch')->count($this->generateQuery())['count']);
        }

        return $this->_result;
    }

    /**
     * Generate Elasticsearch Query.
     *
     * @return array|void
     */
    public function generateQuery(): array
    {
        $query = [
            'index' => IndexHelper::generateIndex($this->_config['input']['index'], $this->_config['input']['frequence'], (! empty($this->_config['input']['separator'])) ? $this->_config['input']['separator'] : null),
            'type' => $this->_config['input']['type'],
            'body' => [
                'query' => $this->_config['input']['query'],
            ],
        ];

        if (! empty($this->_config['input']['size'])) {
            $query['size'] = intval($this->_config['input']['size']);
        }

        return $query;
    }

    /**
     * Launch a complete test.
     *
     * @return bool
     */
    public function launch():bool
    {
        if ($this->_config['enabled']) {
            if ($this->checkThrottle()) {
                $this->updateCheckStatus(self::STATUS_WAITING);
                $this->exec();

                if ($this->conditions()) {
                    $this->updateCheckStatus(self::STATUS_OK, $this->_result);

                    return true;
                } else {
                    $this->updateCheckStatus(self::STATUS_FAIL, $this->_result);

                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
