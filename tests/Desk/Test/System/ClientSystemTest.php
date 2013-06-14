<?php

namespace Desk\Test\System;

use Desk\Client;
use Desk\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 * @group system
 */
class ClientSystemTest extends SystemTestCase
{

    /**
     * @dataProvider dataCreateClientValid
     */
    public function testCreateClientValid($config)
    {
        $client = Client::factory($config);
        $this->assertInstanceOf('Desk\\Client', $client);
    }

    public function dataCreateClientValid()
    {
        return array(
            array(array(
                'subdomain' => 'foo',
                'username'  => 'bar',
                'password'  => 'baz',
            )),
            array(array(
                'base_url' => 'http://foo.example.com/',
                'username' => 'bar',
                'password' => 'baz',
            )),
            array(array(
                'subdomain'       => 'test',
                'consumer_key'    => '123',
                'consumer_secret' => '456',
                'token'           => '789',
                'token_secret'    => '012',
            )),
        );
    }

    /**
     * @dataProvider dataCreateClientInvalid
     * @expectedException Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testCreateClientInvalid($config)
    {
        Client::factory($config);
    }

    public function dataCreateClientInvalid()
    {
        return array(
            array(array(
                'username' => 'foo',
                'password' => 'bar',
            )),
            array(array(
                'base_url' => 'http://foo.example.com/',
            )),
            array(array(
                'subdomain' => 'test',
            )),
            array(array(
                'consumer_key'    => '123',
                'consumer_secret' => '456',
                'token'           => '789',
                'token_secret'    => '012',
            )),
        );
    }

    public function testClientHasOauthPlugin()
    {
        $client = $this->getServiceBuilder()->get('mock');
        $this->setMockResponse($client, 'success');

        $request = $client->get('/foo');
        $request->send();

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertContainsIns('OAuth', $request->getHeader('Authorization'));
    }

    public function testClientHasServiceDescription()
    {
        $client = $this->getServiceBuilder()->get('mock');

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\ServiceDescription',
            $client->getDescription()
        );

        $this->assertSame('Desk.com', $client->getDescription()->getName());
    }

    public function testClientCreatedRequestsHaveCommaAggregator()
    {
        $client = $this->getServiceBuilder()->get('mock');

        $request = $client->get('path/to/resource');
        $this->assertInstanceOf('Guzzle\\Http\\Message\\Request', $request);

        $query = $request->getUrl(true)->getQuery();
        $query->set('foo', array('bar', 'baz'));

        $this->assertSame('foo=bar,baz', (string) $query);
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
