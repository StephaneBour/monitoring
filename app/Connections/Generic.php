<?php

namespace App\Connections;

use App\Helpers\MonitoringHelper;
use App\Interfaces\Connection;

class Generic implements Connection
{
    protected $_config;

    protected $_query;

    protected $_requiredKeys = [];

    protected $_result;

    /**
     * @param array|null $config
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function checkConfig(array $config = null):bool
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
}
