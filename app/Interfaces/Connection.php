<?php

namespace App\Interfaces;

interface Connection
{
    public function checkConfig(array $config = null):bool;

    public function generateQuery():array;
}
