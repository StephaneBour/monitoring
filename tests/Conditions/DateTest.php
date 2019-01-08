<?php

namespace Tests\Connections;

use App\Conditions\Date;
use Tests\TestCase;

class DateTest extends TestCase
{
    private $_app;

    private $_compare;

    public function setUp()
    {
        $this->_app = $this->createApplication();
        $this->_compare = new \DateTime('2018-01-01');
    }

    public function testDateEqual()
    {
        $method = 'equal';
        $this->assertFalse(Date::$method(new \DateTime(), $this->_compare));
        $this->assertFalse(Date::$method(new \DateTime('2017-01-01'), $this->_compare));
        $this->assertTrue(Date::$method($this->_compare, $this->_compare));

        $this->assertTrue(Date::today(new \DateTime(), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method]));
    }

    public function testDateGt()
    {
        $method = 'gt';
        $this->assertTrue(Date::$method(new \DateTime(), $this->_compare));
        $this->assertFalse(Date::$method(new \DateTime('2017-01-01'), $this->_compare));
        $this->assertFalse(Date::$method($this->_compare, $this->_compare));

        $this->assertFalse(Date::today(new \DateTime(), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today(new \DateTime(), ['method' => $method]));
        $this->assertFalse(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method]));
        $this->assertFalse(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method]));
    }

    public function testDateGte()
    {
        $method = 'gte';
        $this->assertTrue(Date::$method(new \DateTime(), $this->_compare));
        $this->assertFalse(Date::$method(new \DateTime('2017-01-01'), $this->_compare));
        $this->assertTrue(Date::$method($this->_compare, $this->_compare));

        $this->assertTrue(Date::today(new \DateTime(), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today(new \DateTime(), ['method' => $method]));
        $this->assertTrue(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method]));
        $this->assertTrue(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method]));
    }

    public function testDateLt()
    {
        $method = 'lt';
        $this->assertTrue(Date::$method(new \DateTime('2017-01-01'), $this->_compare));
        $this->assertFalse(Date::$method(new \DateTime(), $this->_compare));
        $this->assertFalse(Date::$method($this->_compare, $this->_compare));

        $this->assertFalse(Date::today(new \DateTime(), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today(new \DateTime(), ['method' => $method]));
        $this->assertFalse(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method]));
        $this->assertFalse(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method]));
    }

    public function testDateLte()
    {
        $method = 'lte';
        $this->assertTrue(Date::$method(new \DateTime('2017-01-01'), $this->_compare));
        $this->assertFalse(Date::$method(new \DateTime(), $this->_compare));
        $this->assertTrue(Date::$method($this->_compare, $this->_compare));

        $this->assertTrue(Date::today(new \DateTime(), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today(new \DateTime(), ['method' => $method]));
        $this->assertTrue(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertFalse(Date::today((new \DateTime())->add(new \DateInterval('PT1H')), ['method' => $method]));
        $this->assertTrue(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method, 'strict' => false]));
        $this->assertTrue(Date::today((new \DateTime())->sub(new \DateInterval('PT1H')), ['method' => $method]));
    }

    public function testDateRange()
    {
        $this->assertFalse(Date::range(new \DateTime('2017-01-01'), ['min' => new \DateTime('2018-01-01'), 'max' => new \DateTime('2018-02-01')]));
        $this->assertTrue(Date::range(new \DateTime('2018-01-01'), ['min' => new \DateTime('2018-01-01'), 'max' => new \DateTime('2018-02-01')]));
        $this->assertTrue(Date::range(new \DateTime('2018-02-01'), ['min' => new \DateTime('2018-01-01'), 'max' => new \DateTime('2018-02-01')]));
        $this->assertTrue(Date::range(new \DateTime('2018-01-20'), ['min' => new \DateTime('2018-01-01'), 'max' => new \DateTime('2018-02-01')]));
        $this->assertFalse(Date::range(new \DateTime(), ['min' => new \DateTime('2018-01-01'), 'max' => new \DateTime('2018-02-01')]));
    }
}
