<?php

namespace Desk\Test\System;

use Desk\Client;
use Desk\Client\Factory as ClientFactory;
use Desk\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 * @group system
 */
class ClientSystemTest extends SystemTestCase
{

    public function testCreateClientFromSubdomain()
    {
        $client = Client::factory(
            array('subdomain' => 'foo')
        );

        $this->assertInstanceOf('Desk\\Client', $client);
    }

    public function testCreateClientFromBaseUrl()
    {
        $client = Client::factory(
            array('base_url' => 'http://foo.example.com/')
        );

        $this->assertInstanceOf('Desk\\Client', $client);
    }

    /**
     * @expectedException Guzzle\Common\Exception\InvalidArgumentException
     * @expectedExceptionMessage Config must contain a 'subdomain' key
     */
    public function testCreateClientWithInvalidData()
    {
        Client::factory();
    }

    /**
     * @group network
     */
    public function testNetwork()
    {
        $client = $this->getServiceBuilder()->get('test');
        $response = $client->get('')->send();

        $this->assertTrue($response->isSuccessful());

        $result = $response->json();
        $this->assertInternalType('array', $result);
    }
}
