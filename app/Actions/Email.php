<?php

namespace App\Actions;

use App\Interfaces\Action;

class Email implements Action
{
    protected $_config;

    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    public function send()
    {
    }
}
