<?php

namespace App\Interfaces;

interface Connection
{
    public function checkConfig(array $config = null): bool;

    public function checkThrottle(): bool;

    public function condition(): bool;

    public function exec();

    public function generateQuery(): array;
}
