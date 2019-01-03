<?php

namespace App\Actions;

use App\Interfaces\Action;
use App\Mail\Alert;
use Illuminate\Support\Facades\Mail;

class Email implements Action
{
    protected $_config;

    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    public function send()
    {
        Mail::send(new Alert($this->_config));
    }
}
