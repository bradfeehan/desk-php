<?php

namespace Desk\Test\Unit\Iterator;

use Desk\Test\Helper\UnitTestCase;

class ResourceIteratorTest extends UnitTestCase
{

    public function getMockedClass()
    {
        return 'Desk\\Iterator\\ResourceIterator';
    }

    /**
     * @covers Desk\Iterator\ResourceIterator::sendRequest()
     */
    public function testSendRequestForFirstAndOnlyRequest()
    {
        $entries = array('$entry1', '$entry2');

        $result = \Mockery::mock()
            ->shouldReceive('hasLink')
                ->with('next')
                ->andReturn(false)
            ->shouldReceive('getEmbedded')
                ->with('entries')
                ->andReturn($entries)
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface')
            ->shouldReceive('execute')
                ->once()
                ->andReturn($result)
            ->getMock();

        $iterator = $this->mock('sendRequest');
        $this->setPrivateProperty($iterator, 'command', $command);

        $result = $this->callPrivateMethod($iterator, 'sendRequest');
        $this->assertSame($result, $entries);

        $this->assertNull($this->getPrivateProperty($iterator, 'nextToken'));
    }

    /**
     * @covers Desk\Iterator\ResourceIterator::sendRequest()
     */
    public function testSendRequestForFirstRequestWithNextPage()
    {
        $entries = array('$entry1', '$entry2');

        $result = \Mockery::mock()
            ->shouldReceive('hasLink')
                ->with('next')
                ->andReturn(true)
            ->shouldReceive('getLink')
                ->with('next')
                ->andReturn('$nextLink')
            ->shouldReceive('getEmbedded')
                ->with('entries')
                ->andReturn($entries)
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface')
            ->shouldReceive('execute')
                ->once()
                ->andReturn($result)
            ->getMock();

        $iterator = $this->mock('sendRequest');
        $this->setPrivateProperty($iterator, 'command', $command);

        $result = $this->callPrivateMethod($iterator, 'sendRequest');
        $this->assertSame($result, $entries);

        // Assert that next page is saved
        $this->assertSame(
            '$nextLink',
            $this->getPrivateProperty($iterator, 'nextCommand')
        );
    }

    /**
     * @covers Desk\Iterator\ResourceIterator::sendRequest()
     */
    public function testSendRequestForSubsequentRequest()
    {
        $entries = array('$entry3', '$entry4');

        $result = \Mockery::mock()
            ->shouldReceive('hasLink')
                ->with('next')
                ->andReturn(false)
            ->shouldReceive('getEmbedded')
                ->with('entries')
                ->andReturn($entries)
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface')
            ->shouldReceive('execute')
                ->once()
                ->andReturn($result)
            ->getMock();

        $iterator = $this->mock('sendRequest');
        $this->setPrivateProperty($iterator, 'nextToken', true);
        $this->setPrivateProperty($iterator, 'nextCommand', $command);

        $result = $this->callPrivateMethod($iterator, 'sendRequest');
        $this->assertSame($result, $entries);

        $this->assertNull($this->getPrivateProperty($iterator, 'nextToken'));
    }
}
