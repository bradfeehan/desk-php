<?php

namespace Desk\Test\Unit\Relationship\Resource;

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
     * @covers Desk\Relationship\Resource\EmbeddedCommand::setResponse
     */
    public function testSetResponse()
    {
        $response = \Mockery::mock('Desk\\Relationship\\Resource\\EmbeddedResponse');
        $command = $this->mock('setResponse');
        $command->setResponse($response);

        $commandResponse = $this->getPrivateProperty($command, 'response');
        $this->assertSame($response, $commandResponse);
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedCommand::isPrepared
     */
    public function testIsPrepared()
    {
        $this->assertTrue($this->mock('isPrepared')->isPrepared());
    }
}
