<?php

namespace Desk\Test\Unit\Client;

use Desk\Client\CommaAggregatorListener;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Common\Event;

class CommaAggregatorListenerTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Client\\CommaAggregatorListener';
    }

    /**
     * @covers Desk\Client\CommaAggregatorListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $events = CommaAggregatorListener::getSubscribedEvents();
        $this->assertInternalType('array', $events);
    }

    /**
     * @covers Desk\Client\CommaAggregatorListener::setQueryAggregator
     */
    public function testSetQueryAggregator()
    {
        $commaClass = 'Guzzle\\Http\\QueryAggregator\\CommaAggregator';
        $request = \Mockery::mock('Guzzle\\Http\\Message\\Request');
        $request
            ->shouldReceive('getQuery->setAggregator')
                ->with(\Mockery::type($commaClass))
                ->once()
            ->getMock();

        $event = new Event(array('request' => $request));

        $listener = $this->mock('setQueryAggregator');
        $listener->setQueryAggregator($event);

        $this->assertTrue(true);
    }
}
