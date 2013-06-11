<?php

namespace Desk\Test\Unit\Command;

use Desk\Test\Helper\UnitTestCase;

class NullResponseCommandTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Command\\NullResponseCommand';
    }

    /**
     * @covers Desk\Command\NullResponseCommand::process
     */
    public function testProcess()
    {
        $command = $this->mock(array('process', 'getResult'))
            ->shouldReceive('isExecuted')
                ->andReturn(true)
            ->getMock();

        $this->assertNull($command->getResult());
    }
}
