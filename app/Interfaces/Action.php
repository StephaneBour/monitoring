<?php

namespace App\Interfaces;

interface Action
{
    public function __construct(array $config);

    public function send();
}
