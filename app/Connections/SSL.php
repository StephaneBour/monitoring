<?php

namespace App\Connections;

use App\Exceptions\Fail;
use App\Helpers\SSLHelper;
use Carbon\Carbon;

class SSL extends Generic
{
    /**
     * SSL constructor.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
        $this->_requiredKeys = ['input' => ['url', 'type', 'frequence', 'mode'], 'throttle_period', 'actions', 'enabled', 'uuid'];
        $this->checkConfig();
    }

    /**
     * @throws Fail
     *
     * @return int
     */
    public function exec()
    {
        switch ($this->_config['input']['mode']) {
            case 'stillValid':
                switch ($this->_config['input']['type']) {
                    case 'bool':
                        $this->_config['conditions'] = ['bool' => ['isTrue']];
                        $this->_result = SSLHelper::stillValid($this->_config['input']['url']);
                        break;
                    case 'date':
                        $this->_config['conditions'] = ['date' => ['today' => ['method' => 'gt']]];
                        $this->_result = (new \DateTime())->setTimestamp(SSLHelper::validFrom($this->_config['input']['url']));
                        break;
                    case 'diff':
                        $this->_config['conditions'] = ['count' => ['gte' => $this->_config['input']['days']]];
                        $date = Carbon::createFromTimestamp(SSLHelper::validFrom($this->_config['input']['url']));
                        $this->_result = $date->diffInDays();
                        if ($this->_result <= $this->_config['input']['days']) {
                            throw new Fail('The SSL certificate expires in ' . $this->_result . ' days');
                        }
                        break;
                }
                break;
        }

        return $this->_result;
    }
}
