<?php

namespace Desk\Test\Unit\Iterator;

use Desk\Test\Helper\UnitTestCase;

class FactoryTest extends UnitTestCase
{

    public function getMockedClass()
    {
        return 'Desk\\Iterator\\Factory';
    }

    /**
     * @covers Desk\Iterator\Factory::getClassName
     */
    public function testCanBuildTrue()
    {
        $factory = $this->mock('canBuild');
        $this->assertTrue(
            $factory->canBuild($this->command('ListWidgets'))
        );
    }

    /**
     * @covers Desk\Iterator\Factory::getClassName
     */
    public function testCanBuildFalse()
    {
        $factory = $this->mock('canBuild');
        $this->assertFalse(
            $factory->canBuild($this->command('ShowWidget'))
        );
    }

    /**
     * @covers Desk\Iterator\Factory::getClassName
     */
    public function testBuild()
    {
        $factory = $this->mock(array('build', 'canBuild'));
        $this->assertInstanceOf(
            'Desk\\Iterator\\ResourceIterator',
            $factory->build($this->command('ListWidgets'))
        );
    }

    /**
     * @covers Desk\Iterator\Factory::getClassName
     * @expectedException Guzzle\Common\Exception\InvalidArgumentException
     * @expectedExceptionMessage Iterator was not found for ShowWidget
     */
    public function testBuildForInvalidCommand()
    {
        $factory = $this->mock(array('build', 'canBuild'));
        $factory->build($this->command('ShowWidget'));
    }

    /**
     * Creates a Guzzle command, optionally with a particular name set
     *
     * @return \Guzzle\Service\Command\CommandInterface
     */
    private function command($commandName = null)
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');

        if ($commandName) {
            $command->shouldReceive('getName')->andReturn($commandName);
        }

        return $command;
    }
}
