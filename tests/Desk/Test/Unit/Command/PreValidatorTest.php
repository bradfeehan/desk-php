<?php

namespace Desk\Test\Unit\Command;

use Desk\Command\PreValidator;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Common\Event;
use Guzzle\Common\ToArrayInterface;
use Guzzle\Service\Client;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Description\Operation;

class PreValidatorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getMockedClass()
    {
        return 'Desk\\Command\\PreValidator';
    }

    /**
     * @covers Desk\Command\PreValidator::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $events = PreValidator::getSubscribedEvents();
        $this->assertInternalType('array', $events);
    }

    /**
     * @covers Desk\Command\PreValidator::castPrimitivesToArrays
     * @dataProvider dataCastPrimitivesToArrays
     *
     * @param array $param    Parameter description/structure
     * @param mixed $value    Input value for the parameter
     * @param mixed $expected Expected output value
     */
    public function testCastPrimitivesToArrays(array $param, $value, $expected)
    {
        $command = new OperationCommand(
            array('foo' => $value),
            new Operation(array('parameters' => array('foo' => $param)))
        );

        $command->setClient(new Client());
        $event = new Event(array('command' => $command));

        $preValidator = $this->mock('castPrimitivesToArrays');
        $preValidator->castPrimitivesToArrays($event);

        $this->assertSame($expected, $command->get('foo'));
    }

    public function dataCastPrimitivesToArrays($command)
    {
        return array(
            array(
                array(
                    'type' => 'array',
                    'items' => array('type' => 'integer')
                ),
                36,        // value
                array(36), // expected
            ),
            array(
                array(
                    'type' => 'array',
                    'items' => array('type' => 'integer')
                ),
                null, // value
                null, // expected
            ),
            array(
                array(
                    'type' => 'array',
                    'items' => array('type' => 'integer')
                ),
                \Mockery::mock('Guzzle\\Common\\ToArrayInterface')
                    ->shouldReceive('toArray')
                        ->andReturn(array(45))
                    ->shouldReceive('__toString')
                        ->andReturn('ToArrayInterface')
                    ->getMock(),
                \Mockery::self(), // last mock object created
            ),
            array(
                array('type' => 'string'),
                'test', // value
                'test', // expected (parameter allows primitive string)
            ),
        );
    }

    /**
     * @covers Desk\Command\PreValidator::castPrimitivesToArrays
     */
    public function testCastPrimitivesToArraysIgnoresBadEvents()
    {
        $event = new Event(array('command' => new \stdClass()));

        $preValidator = $this->mock('castPrimitivesToArrays');
        $preValidator->castPrimitivesToArrays($event);

        $this->assertTrue(true); // assert no exception thrown
    }
}
