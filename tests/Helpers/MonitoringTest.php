<?php

namespace Tests\Helpers;

use App\Helpers\MonitoringHelper;
use Tests\TestCase;

class MonitoringTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testConvertMultidimensionalKeysToUnique()
    {
        $checks = ['enabled', 'input', 'input.index', 'input.type', 'input.frequence', 'input.mode', 'input.query', 'condition', 'throttle_period', 'actions'];
        $keys = MonitoringHelper::convertMultidimensionalKeysToUnique(['enabled', 'input' => ['index', 'type', 'frequence', 'mode', 'query'], 'condition', 'throttle_period', 'actions']);
        foreach ($checks as $check) {
            if (! in_array($check, $keys)) {
                $this->fail($check . ' not exists in return');
            }
            $this->assertTrue(in_array($check, $keys));
        }
    }
}
