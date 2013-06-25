<?php

namespace Desk\Test\Unit\Relationship;

use Desk\Relationship\Plugin;
use Desk\Relationship\ResponseParser;
use Desk\Test\Helper\UnitTestCase;

class PluginTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Plugin';
    }

    /**
     * @covers Desk\Relationship\Plugin::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $events = Plugin::getSubscribedEvents();
        $this->assertInternalType('array', $events);
    }

    /**
     * @covers Desk\Relationship\Plugin::__construct
     */
    public function testConstruct()
    {
        $plugin = new Plugin();
        $parser = $this->getPrivateProperty($plugin, 'parser');
        $this->assertNull($parser);
    }

    /**
     * @covers Desk\Relationship\Plugin::getResponseParser
     */
    public function testGetResponseParserWithOverriddenParser()
    {
        $parserClass = 'Guzzle\\Service\\Command\\ResponseParserInterface';
        $parser = \Mockery::mock($parserClass);
        $plugin = $this->mock('getResponseParser', array($parser));

        $result = $plugin->getResponseParser();
        $this->assertSame($parser, $result);
    }

    /**
     * @covers Desk\Relationship\Plugin::onCreateCommand
     */
    public function testOnCreateCommand()
    {
        $parserClass = 'Guzzle\\Service\\Command\\ResponseParserInterface';
        $parser = \Mockery::mock($parserClass);

        $command = \Mockery::mock('Guzzle\\Service\\Command\\OperationCommand')
            ->shouldReceive('setResponseParser')
                ->with($parser)
            ->getMock();

        $event = \Mockery::mock('Guzzle\\Common\\Event')
            ->shouldReceive('offsetGet')
                ->with('command')
                ->andReturn($command)
            ->getMock();

        $plugin = $this->mock('onCreateCommand')
            ->shouldReceive('getResponseParser')
                ->andReturn($parser)
            ->getMock();

        $plugin->onCreateCommand($event);

        $this->assertSame($command, $event['command']);
    }

    /**
     * @covers Desk\Relationship\Plugin::onCreateCommand
     */
    public function testOnCreateCommandWithNonOperationCommand()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $event = \Mockery::mock('Guzzle\\Common\\Event')
            ->shouldReceive('offsetGet')
                ->with('command')
                ->andReturn($command)
            ->getMock();

        $this->mock('onCreateCommand')->onCreateCommand($event);

        $this->assertSame($command, $event['command']);
    }
}
