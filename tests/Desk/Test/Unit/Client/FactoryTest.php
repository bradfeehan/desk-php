<?php

namespace Desk\Test\Unit\Client;

use Desk\Client;
use Desk\Client\Factory as ClientFactory;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Common\Collection;

class FactoryTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getMockedClass()
    {
        return 'Desk\\Client\\Factory';
    }

    /**
     * @covers Desk\Client\Factory::__construct
     */
    public function testConstruct()
    {
        $loader = \Mockery::mock('Desk\\Client\\ServiceDescriptionLoader');
        $factory = new ClientFactory($loader);

        $factoryLoader = $this->getPrivateProperty($factory, 'loader');
        $this->assertSame($loader, $factoryLoader);
    }

    /**
     * @covers Desk\Client\Factory::factory
     */
    public function testFactory()
    {
        $factory = $this->mock('factory')
            ->shouldReceive('processConfig')
                ->with(array())
                ->andReturn(array('base_url' => 'http://mock.localhost/'))
            ->shouldReceive('addAuthentication')
            ->shouldReceive('addServiceDescription')
            ->shouldReceive('addRelationshipPlugin')
            ->shouldReceive('addCommaAggregatorListener')
            ->shouldReceive('addPreValidator')
            ->getMock();

        $client = $factory->factory();

        $this->assertInstanceOf('Desk\\Client', $client);
        $this->assertSame('http://mock.localhost/', $client->getBaseUrl());
    }

    /**
     * @covers Desk\Client\Factory::processConfig
     * @dataProvider dataProcessConfigValid
     */
    public function testProcessConfigValid($config, $expectedArray)
    {
        $factory = $this->mock('processConfig');
        $actual = $factory->processConfig($config);
        $this->assertInstanceOf('Guzzle\\Common\\Collection', $actual);

        $actualArray = $actual->toArray();
        $this->assertSame($expectedArray, $actualArray);
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
                array(
                    'api_version' => 2,
                    'base_url' => 'https://{subdomain}.desk.com/api/v{api_version}/',
                    'subdomain' => 'foo',
                    'username' => 'foo',
                    'password' => 'bar',
                    'authentication' => 'basic',
                ),
            ),
            array(
                array(
                    'base_url' => 'http://example.com/',
                    'username' => 'foo',
                    'password' => 'bar',
                ),
                array(
                    'api_version' => 2,
                    'base_url' => 'http://example.com/',
                    'username' => 'foo',
                    'password' => 'bar',
                    'authentication' => 'basic',
                ),
            ),
            array(
                array(
                    'base_url' => 'http://{subdomain}.example.com/',
                    'subdomain' => 'foo',
                    'username' => 'foo',
                    'password' => 'bar',
                ),
                array(
                    'api_version' => 2,
                    'base_url' => 'http://{subdomain}.example.com/',
                    'subdomain' => 'foo',
                    'username' => 'foo',
                    'password' => 'bar',
                    'authentication' => 'basic',
                ),
            ),
            array(
                array(
                    'subdomain'       => 'foo',
                    'consumer_key'    => '123',
                    'consumer_secret' => '456',
                    'token'           => '789',
                    'token_secret'    => '012',
                ),
                array(
                    'api_version' => 2,
                    'base_url' => 'https://{subdomain}.desk.com/api/v{api_version}/',
                    'subdomain' => 'foo',
                    'consumer_key' => '123',
                    'consumer_secret' => '456',
                    'token' => '789',
                    'token_secret' => '012',
                    'authentication' => 'oauth',
                ),
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
        $factory = new ClientFactory();

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
        $factory = new ClientFactory();

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
    }

    /**
     * @covers Desk\Client\Factory::addAuthentication
     * @expectedException Desk\Exception\InvalidArgumentException
     */
    public function testAddAuthenticationInvalid()
    {
        $factory = new ClientFactory();
        $client = new Client('http://mock.localhost/', new Collection(array()));

        $factory->addAuthentication($client);
    }

    /**
     * @covers Desk\Client\Factory::addServiceDescription
     */
    public function testAddServiceDescription()
    {
        $testCase = $this;

        $description = \Mockery::mock(
            'Guzzle\Service\Description\ServiceDescriptionInterface'
        );

        $loader = \Mockery::mock(
            'Guzzle\Service\Description\ServiceDescriptionLoader',
            array('load' => $description)
        );

        $client = \Mockery::mock('Desk\\Client')
            ->shouldReceive('setDescription')
                ->with($description)
                ->once()
            ->getMock();

        $factory = new ClientFactory($loader);
        $factory->addServiceDescription($client);

        // the real assertion is done by Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Client\Factory::addRelationshipPlugin
     */
    public function testAddRelationshipPlugin()
    {
        $originalClient = \Mockery::mock('Desk\\Client')
            ->shouldReceive('addSubscriber')
                ->with(\Mockery::type('Desk\\Relationship\\Plugin'))
                ->once()
            ->getMock();

        $client = $originalClient;

        $factory = $this->mock('addRelationshipPlugin');
        $factory->addRelationshipPlugin($client);
        $this->assertSame($originalClient, $client);
    }

    /**
     * @covers Desk\Client\Factory::addCommaAggregatorListener
     */
    public function testAddCommaAggregatorListener()
    {
        $originalClient = \Mockery::mock('Desk\\Client')
            ->shouldReceive('addSubscriber')
                ->with(\Mockery::type('Desk\\Client\\CommaAggregatorListener'))
                ->once()
            ->getMock();

        $client = $originalClient;

        $factory = $this->mock('addCommaAggregatorListener');
        $factory->addCommaAggregatorListener($client);
        $this->assertSame($originalClient, $client);
    }

    /**
     * @covers Desk\Client\Factory::addPreValidator
     */
    public function testAddPreValidator()
    {
        $originalClient = \Mockery::mock('Desk\\Client')
            ->shouldReceive('addSubscriber')
                ->with(\Mockery::type('Desk\\Command\\PreValidator'))
                ->once()
            ->getMock();

        $client = $originalClient;

        $factory = $this->mock('addPreValidator');
        $factory->addPreValidator($client);
        $this->assertSame($originalClient, $client);
    }
}
