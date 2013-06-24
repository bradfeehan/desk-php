<?php

namespace Desk\Test\Unit\Relationship\Resource;

use Desk\Test\Helper\UnitTestCase;

class EmbeddedCommandFactoryTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Resource\\EmbeddedCommandFactory';
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommandFactory::factory
     */
    public function testFactory()
    {
        $client = \Mockery::mock('Guzzle\\Service\\ClientInterface');
        $originalResponse = \Mockery::mock('Guzzle\\Http\\Message\\Response');
        $embeddedResponse = \Mockery::mock('Guzzle\\Http\\Message\\Response');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface')
            ->shouldReceive('getClient')
                ->andReturn($client)
            ->shouldReceive('getResponse')
                ->andReturn($originalResponse)
            ->getMock();

        $data = array('foo' => 'bar');

        $embeddedClass = 'Desk\\Relationship\\Resource\\EmbeddedCommand';
        $embedded = \Mockery::mock($embeddedClass)
            ->shouldReceive('setClient')
                ->with($client)
            ->shouldReceive('setResponse')
                ->with($embeddedResponse)
            ->getMock();

        $factory = $this->mock('factory')
            ->shouldReceive('newCommand')
                ->andReturn($embedded)
            ->shouldReceive('createResponse')
                ->with($originalResponse, $data)
                ->andReturn($embeddedResponse)
            ->getMock();

        $result = $factory->factory($command, $data);

        $this->assertSame($embedded, $result);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommandFactory::newCommand
     */
    public function testNewCommand()
    {
        $factory = $this->mock('newCommand');
        $command = $factory->newCommand();

        $embeddedClass = 'Desk\\Relationship\\Resource\\EmbeddedCommand';
        $this->assertInstanceOf($embeddedClass, $command);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommandFactory::createResponse
     */
    public function testCreateResponse()
    {
        $headers = \Mockery::mock('Guzzle\\Common\\Collection');

        $originalResponse = \Mockery::mock('Guzzle\\Http\\Message\\Response')
            ->shouldReceive('getStatusCode')
                ->andReturn(200)
            ->shouldReceive('getReasonPhrase')
                ->andReturn('Hello, World!')
            ->shouldReceive('getHeaders')
                ->andReturn($headers)
            ->getMock();

        $embeddedResponse = \Mockery::mock('Guzzle\\Http\\Message\\Response')
            ->shouldReceive('setReasonPhrase')
                ->with('Hello, World!')
            ->getMock();

        $data = array('foo' => 'bar');

        $factory = $this->mock('createResponse')
            ->shouldReceive('newResponse')
                ->with(200, $headers, '{"foo":"bar"}')
                ->andReturn($embeddedResponse)
            ->getMock();

        $response = $factory->createResponse($originalResponse, $data);
        $this->assertSame($embeddedResponse, $response);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommandFactory::newResponse
     */
    public function testNewResponse()
    {
        $factory = $this->mock('newResponse');
        $response = $factory->newResponse(304);

        $embeddedClass = 'Desk\\Relationship\\Resource\\EmbeddedResponse';
        $this->assertInstanceOf($embeddedClass, $response);
        $this->assertSame(304, $response->getStatusCode());
    }
}
