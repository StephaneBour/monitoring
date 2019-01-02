<?php

namespace Tests\Connections;

use App\Connections\Generic;
use Tests\TestCase;

class GenericTest extends TestCase
{
    public function testEmptyConfig()
    {
        $connection = new Generic();
        try {
            $connection->checkConfig();
            $this->fail('Expected Exception has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals($ex->getMessage(), 'Your ' . Generic::class . ' config is empty');
        }
    }
}
