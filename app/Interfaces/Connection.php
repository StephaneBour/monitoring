<?php

namespace App\Interfaces;

interface Connection
{
    const STATUS_FAIL = 'FAIL';
    const STATUS_OK = 'OK';
    const STATUS_WAITING = 'WAITING';

    public function checkConfig(array $config = null): bool;

    public function checkThrottle(): bool;

    public function conditions(): bool;

    public function exec();

    public function generateQuery(): array;

    public function launch(): bool;
}
