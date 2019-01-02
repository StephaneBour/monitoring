<?php

namespace Tests\Helpers;

use App\Helpers\IndexHelper;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndexGeneretor()
    {
        $this->assertEquals(config('elasticsearch.index.name').'_' . config('elasticsearch.index.results.prefix') . '-' . date('Y-m-d'), IndexHelper::generateResultIndex());
    }
}
