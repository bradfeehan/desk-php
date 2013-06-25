<?php

namespace Desk\Test\Unit\Relationship\Resource;

use Desk\Relationship\Resource\EmbeddedCommand;
use Desk\Test\Helper\UnitTestCase;

class EmbeddedCommandTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Resource\\EmbeddedCommand';
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getName
     */
    public function testGetName()
    {
        $command = $this->mock('getName');
        $command
            ->shouldReceive('getOperation->getName')
                ->andReturn('$result');

        $this->assertSame('$result', $command->getName());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getOperation
     */
    public function testGetOperation()
    {
        $operation = $this->mock('getOperation')->getOperation();
        $operationClass = 'Guzzle\\Service\\Description\\Operation';
        $this->assertInstanceOf($operationClass, $operation);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::isExecuted
     */
    public function testIsExecuted()
    {
        $this->assertTrue($this->mock('isExecuted')->isExecuted());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::execute
     */
    public function testExecute()
    {
        $command = $this->mock('execute')
            ->shouldReceive('getResult')
                ->andReturn('$result')
            ->getMock();

        $this->assertSame('$result', $command->execute());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getClient
     */
    public function testGetClient()
    {
        $client = \Mockery::mock('Guzzle\\Service\\ClientInterface');
        $command = $this->mock('getClient');
        $this->setPrivateProperty($command, 'client', $client);
        $this->assertSame($client, $command->getClient());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::setClient
     */
    public function testSetClient()
    {
        $client = \Mockery::mock('Guzzle\\Service\\ClientInterface');
        $command = $this->mock('setClient');
        $command->setClient($client);

        $commandClient = $this->getPrivateProperty($command, 'client');
        $this->assertSame($client, $commandClient);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getRequest
     */
    public function testGetRequest()
    {
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $command = $this->mock('getRequest');
        $this->setPrivateProperty($command, 'request', $request);
        $this->assertSame($request, $command->getRequest());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::setRequest
     */
    public function testSetRequest()
    {
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $command = $this->mock('setRequest');
        $command->setRequest($request);

        $commandRequest = $this->getPrivateProperty($command, 'request');
        $this->assertSame($request, $commandRequest);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getResponse
     */
    public function testGetResponse()
    {
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response');
        $command = $this->mock('getResponse');
        $this->setPrivateProperty($command, 'response', $response);
        $this->assertSame($response, $command->getResponse());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::setResponse
     */
    public function testSetResponse()
    {
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response');
        $command = $this->mock('setResponse');
        $command->setResponse($response);

        $commandResponse = $this->getPrivateProperty($command, 'response');
        $this->assertSame($response, $commandResponse);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getResult
     */
    public function testGetResult()
    {
        $result = \Mockery::mock('setResult');
        $command = $this->mock('getResult');
        $this->setPrivateProperty($command, 'result', $result);
        $this->assertSame($result, $command->getResult());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::setResult
     */
    public function testSetResult()
    {
        $command = $this->mock('setResult');
        $command->setResult('$result');

        $commandResult = $this->getPrivateProperty($command, 'result');
        $this->assertSame('$result', $commandResult);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::isPrepared
     */
    public function testIsPrepared()
    {
        $this->assertTrue($this->mock('isPrepared')->isPrepared());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::prepare
     */
    public function testPrepare()
    {
        $request = \Mockery::mock('Guzzle\\Http\\Message\\Request');

        $command = $this->mock('prepare')
            ->shouldReceive('getRequest')
                ->andReturn($request)
            ->getMock();

        $this->assertSame($request, $command->prepare());
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::getRequestHeaders
     */
    public function testGetRequestHeaders()
    {
        $command = $this->mock('getRequestHeaders');
        $headers = $command->getRequestHeaders();
        $this->assertInstanceOf('Guzzle\\Common\\Collection', $headers);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::setOnComplete
     */
    public function testSetOnComplete()
    {
        $command = $this->mock('setOnComplete');
        $this->assertNull($command->setOnComplete('foo'));
    }
}
