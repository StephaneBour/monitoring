<?php

namespace Tests\Connections;

use App\Conditions\Count;
use Tests\TestCase;

class CountTest extends TestCase
{
    private $_app;

    public function setUp()
    {
        $this->_app = $this->createApplication();
    }

    public function testCountGt()
    {
        $this->assertTrue(Count::gt(10, 4));
        $this->assertFalse(Count::gt(4, 10));
        $this->assertFalse(Count::gt(10, 10));
    }

    public function testCountGte()
    {
        $this->assertTrue(Count::gte(10, 4));
        $this->assertFalse(Count::gte(4, 10));
        $this->assertTrue(Count::gte(10, 10));
    }

    public function testCountLt()
    {
        $this->assertTrue(Count::lt(4, 10));
        $this->assertFalse(Count::lt(10, 4));
        $this->assertFalse(Count::lt(10, 10));
    }

    public function testCountLte()
    {
        $this->assertTrue(Count::lte(4, 10));
        $this->assertFalse(Count::lte(10, 4));
        $this->assertTrue(Count::lte(10, 10));
    }

    public function testCountEqual()
    {
        $this->assertFalse(Count::equal(4, 10));
        $this->assertFalse(Count::equal(10, 4));
        $this->assertTrue(Count::equal(10, 10));
    }

    public function testCountRange()
    {
        $this->assertFalse(Count::range(4, ['min' => 6, 'max' => 12]));
        $this->assertTrue(Count::range(4, ['min' => 4, 'max' => 12]));
        $this->assertTrue(Count::range(12, ['min' => 4, 'max' => 12]));
        $this->assertTrue(Count::range(6, ['min' => 4, 'max' => 12]));
        $this->assertFalse(Count::range(13, ['min' => 6, 'max' => 12]));
    }
}
