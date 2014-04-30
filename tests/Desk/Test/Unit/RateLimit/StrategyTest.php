<?php

namespace Desk\Test\Unit\RateLimit;

use Desk\Test\Helper\UnitTestCase;
use Desk\RateLimit\Strategy;

class StrategyTest extends UnitTestCase
{

    protected function getMockedClass()
    {
        return 'Desk\\RateLimit\\Strategy';
    }

    public function testConstruct()
    {
        $strategy = new Strategy();
        $this->assertInstanceOf($this->getMockedClass(), $strategy);
    }

    public function testGetMultiplier()
    {
        $strategy = new Strategy(3);
        $this->assertSame(3.0, $strategy->getMultiplier());
    }

    public function testGetBackoffPeriodWithNoResponse()
    {
        $strategy = $this->mock('getBackoffPeriod');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $this->assertFalse($strategy->getBackoffPeriod(0, $request));
    }

    public function testGetBackoffPeriodWithSuccessfulResponse()
    {
        $strategy = $this->mock('getBackoffPeriod');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response')
            ->shouldReceive('getStatusCode')
                ->andReturn(200)
            ->getMock();

        $result = $strategy->getBackoffPeriod(0, $request, $response);
        $this->assertFalse($result);
    }

    public function testGetBackoffPeriodWithRateLimitExceededResponse()
    {
        $strategy = $this->mock('getBackoffPeriod')
            ->shouldReceive('getMultiplier')
                ->andReturn(1)
            ->getMock();

        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response')
            ->shouldReceive('getStatusCode')
                ->andReturn(429)
            ->shouldReceive('getHeader')
                ->with('X-Rate-Limit-Reset')
                ->andReturn('12')
            ->getMock();

        $result = $strategy->getBackoffPeriod(0, $request, $response);
        $this->assertSame(12.1, $result);
    }

    public function testGetBackoffPeriodWithInvalidXRateLimitResetHeader()
    {
        $strategy = $this->mock('getBackoffPeriod');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response')
            ->shouldReceive('getStatusCode')
                ->andReturn(429)
            ->shouldReceive('getHeader')
                ->with('X-Rate-Limit-Reset')
                ->andReturn('lalala')
            ->getMock();

        $result = $strategy->getBackoffPeriod(0, $request, $response);
        $this->assertFalse($result);
    }
}
