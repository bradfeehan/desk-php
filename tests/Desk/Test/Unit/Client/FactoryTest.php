<?php

namespace Desk\Test\Unit\Client;

use Desk\Client;
use Desk\Client\Factory as ClientFactory;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Common\Collection;

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
     * {@inheritdoc}
     */
    protected function getMockedClass()
    {
        return 'Desk\\Client\\Factory';
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
        $factory = $this->mock(array('processConfig', 'addAuthentication'))
            ->shouldReceive('processConfig')
                ->with(array())
                ->andReturn(array('base_url' => 'http://mock.localhost/'))
            ->shouldReceive('addAuthentication')
            ->getMock();

        $client = $factory->factory();

        $this->assertInstanceOf('Desk\\Client', $client);
        $this->assertSame('http://mock.localhost/', $client->getBaseUrl());
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
                array(
                    'subdomain' => 'foo',
                    'username' => 'foo',
                    'password' => 'bar',
                ),
                'https://foo.desk.com/api/v2/'
            ),
            array(
                array(
                    'base_url' => 'http://example.com/',
                    'username' => 'foo',
                    'password' => 'bar',
                ),
                'http://example.com/'
            ),
            array(
                array(
                    'base_url' => 'http://{subdomain}.example.com/',
                    'subdomain' => 'foo',
                    'username' => 'foo',
                    'password' => 'bar',
                ),
                'http://foo.example.com/'
            ),
            array(
                array(
                    'subdomain'       => 'foo',
                    'consumer_key'    => '123',
                    'consumer_secret' => '456',
                    'token'           => '789',
                    'token_secret'    => '012',
                ),
                'https://foo.desk.com/api/v2/'
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

    /**
     * @covers Desk\Client\Factory::addAuthentication
     */
    public function testAddAuthenticationWithBasicAuth()
    {
        $factory = ClientFactory::instance();

        $client = new Client(
            'http://mock.localhost/',
            new Collection(
                array(
                    'authentication' => 'basic',
                    'username' => 'user',
                    'password' => 'pass',
                )
            )
        );

        $factory->addAuthentication($client);

        $headers = $client->getDefaultHeaders();
        $this->assertTrue($headers->hasKey('Authorization'));
        $this->assertSame('Basic dXNlcjpwYXNz', $headers->get('Authorization'));
    }

    /**
     * @covers Desk\Client\Factory::addAuthentication
     */
    public function testAddAuthenticationWithOauth()
    {
        $factory = ClientFactory::instance();

        $client = new Client(
            'http://mock.localhost/',
            new Collection(
                array(
                    'authentication'  => 'oauth',
                    'consumer_key'    => 'foo',
                    'consumer_secret' => 'bar',
                    'token'           => 'baz',
                    'token_secret'    => 'qux',
                )
            )
        );

        $factory->addAuthentication($client);

        $listeners = $client
            ->getEventDispatcher()
            ->getListeners('request.before_send');

        $this->assertSame(1, count($listeners));

        list($listener, $method) = $listeners[0];

        $this->assertSame('Guzzle\\Plugin\\Oauth\\OauthPlugin', get_class($listener));
        $this->assertSame('onRequestBeforeSend', $method);

        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Client\Factory::addAuthentication
     * @expectedException Desk\Exception\InvalidArgumentException
     */
    public function testAddAuthenticationInvalid()
    {
        $factory = ClientFactory::instance();
        $client = new Client('http://mock.localhost/', new Collection(array()));

        $factory->addAuthentication($client);
    }
}
