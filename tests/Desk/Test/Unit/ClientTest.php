<?php

namespace Desk\Test\Unit;

use Desk\Client;
use Desk\Client\Factory as ClientFactory;
use Desk\Test\Helper\UnitTestCase;

class ClientTest extends UnitTestCase
{

    public function setUp()
    {
        $this->clearInstance();
    }

    public function tearDown()
    {
        $this->clearInstance();
    }

    /**
     * Clears the singleton instance stored in the Factory
     */
    private function clearInstance()
    {
        ClientFactory::setInstance();
    }

    /**
     * @covers Desk\Client::factory
     */
    public function testFactory()
    {
        $factory = \Mockery::mock('Desk\\Client\\Factory')
            ->shouldReceive('factory')
            ->with(array('foo' => 'bar'))
            ->andReturn('return value')
            ->getMock();

        ClientFactory::setInstance($factory);

        $result = Client::factory(array('foo' => 'bar'));
        $this->assertSame('return value', $result);
    }
}
