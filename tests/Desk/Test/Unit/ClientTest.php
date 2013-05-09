<?php

namespace Desk\Test\Unit;

use Desk\Client;
use Desk\Client\Factory as ClientFactory;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Common\Collection;

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
     * {@inheritdoc}
     */
    protected function getMockedClass()
    {
        return 'Desk\\Client';
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

    /**
     * @covers Desk\Client::setAuth
     */
    public function testSetAuth()
    {
        $client = $this->mock('addDefaultHeader')
            ->shouldReceive('addDefaultHeader')
            ->with('Authorization', 'Basic Zm9vOmJhcg==')
            ->andReturn(\Mockery::self())
            ->getMock();

        $this->assertSame($client, $client->setAuth('foo', 'bar'));
    }

    /**
     * @covers Desk\Client::addDefaultHeader
     */
    public function testAddDefaultHeader()
    {
        $client = $this->mock(array('getDefaultHeaders', 'setDefaultHeaders'))
            ->shouldReceive('getDefaultHeaders')
                ->andReturn(new Collection(array('foo' => 'bar')))
            ->shouldReceive('setDefaultHeaders')
                ->with(
                    \Mockery::on(
                        function ($headers) {
                            return
                                $headers->hasKey('baz') &&
                                $headers->get('baz') === 'qux'
                            ;
                        }
                    )
                )
            ->getMock();

        $this->assertSame($client, $client->addDefaultHeader('baz', 'qux'));
    }
}
