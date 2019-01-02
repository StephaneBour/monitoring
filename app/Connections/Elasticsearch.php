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
        $this->_requiredKeys = ['input' => ['index', 'type', 'frequence', 'mode', 'query'], 'condition', 'throttle_period', 'actions', 'enabled'];
        $this->checkConfig();
        $this->generateQuery();
    }

    /**
     * @return array|void
     */
    public function generateQuery():array
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
     * Check if the return fulfill the condition.
     *
     * @return bool
     */
    public function condition()
    {
        reset($this->_config['condition']);
        $mode = key($this->_config['condition']);
        $class = '\App\Conditions\\' . ucfirst(strtolower($mode));
        reset($this->_config['condition'][$mode]);
        $method = key($this->_config['condition'][$mode]);

        return $class::$method($this->_result, $this->_config['condition'][$mode][$method]);
    }
}
