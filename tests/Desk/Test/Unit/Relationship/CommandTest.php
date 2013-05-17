<?php

namespace Desk\Test\Unit\Relationship;

use Desk\Relationship\Command;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Service\Description\Operation;

class CommandTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Command';
    }

    /**
     * @covers Desk\Relationship\Command::setUri
     */
    public function testSetUri()
    {
        $operation = new Operation();
        $command = new Command(null, $operation);

        $command->setUri('/path/to/uri');

        // The command's operation should have been updated...
        $this->assertSame('/path/to/uri', $command->getOperation()->getUri());

        // but not the original.
        $this->assertNotSame('/path/to/uri', $operation->getUri());

        // They should be different objects.
        $this->assertNotSame($operation, $command->getOperation());

        // The command should not need validation
        $this->assertTrue($command->get(Command::DISABLE_VALIDATION));
    }
}
