<?php

namespace Desk\Test\Unit\Client;

use Desk\Client;
use Desk\Client\Factory as ClientFactory;
use Desk\Test\Helper\UnitTestCase;

class FactoryTest extends UnitTestCase
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
     * Creates a mock of Desk\Client\Factory
     *
     * All methods will be passed through to the underlying
     * implementation, except for method names passed in to $methods
     * (these can be stubbed using shouldReceive()... etc).
     *
     * @param array $methods Any methods that will be overridden
     *
     * @return Desk\Client\Factory
     */
    private function mock($methods = array())
    {
        $methods = implode(',', $methods);
        return \Mockery::mock("Desk\\Client\\Factory[{$methods}]");
    }

    /**
     * @covers Desk\Client\Factory::instance
     */
    public function testInstance()
    {
        $result = ClientFactory::instance();
        $this->assertInstanceOf('Desk\\Client\\Factory', $result);
    }

    /**
     * @covers Desk\Client\Factory::setInstance
     */
    public function testSetInstance()
    {
        $instance = \Mockery::mock('Desk\\Client\\Factory');
        ClientFactory::setInstance($instance);
        $this->assertSame($instance, ClientFactory::instance());
    }

    /**
     * @covers Desk\Client\Factory::factory
     */
    public function testFactory()
    {
        $factory = $this->mock(array('processConfig'))
            ->shouldReceive('processConfig')
            ->with(array())
            ->andReturn(array('base_url' => 'http://foo.example.com/'))
            ->getMock();

        $client = $factory->factory();

        $this->assertInstanceOf('Desk\\Client', $client);
        $this->assertSame('http://foo.example.com/', $client->getBaseUrl());
    }

    /**
     * @covers Desk\Client\Factory::processConfig
     * @dataProvider dataProcessConfigValid
     */
    public function testProcessConfigValid($config, $expectedBaseUrl)
    {
        $result = Client::factory($config);
        $this->assertInstanceOf('Desk\\Client', $result);
        $this->assertSame($expectedBaseUrl, $result->getBaseUrl());
    }

    public function dataProcessConfigValid()
    {
        return array(
            array(
                array('subdomain' => 'foo'),
                'https://foo.desk.com/api/v2/'
            ),
            array(
                array('base_url' => 'http://example.com/'),
                'http://example.com/'
            ),
            array(
                array(
                    'base_url' => 'http://{subdomain}.example.com/',
                    'subdomain' => 'foo',
                ),
                'http://foo.example.com/'
            ),
        );
    }

    /**
     * @covers Desk\Client\Factory::processConfig
     * @dataProvider dataProcessConfigInvalid
     * @expectedException Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testProcessConfigInvalid($config)
    {
        Client::factory($config);
    }

    public function dataProcessConfigInvalid()
    {
        return array(
            array(array()),
            array(array('base_url' => 'http://{subdomain}.example.com/')),
        );
    }
}
