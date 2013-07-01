<?php

namespace Desk\Test\Unit\Relationship\Visitor;

use Desk\Relationship\Visitor\ResponseVisitor;
use Desk\Test\Helper\UnitTestCase;

class ResponseVisitorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Visitor\\ResponseVisitor';
    }

    /**
     * @covers Desk\Relationship\Visitor\ResponseVisitor::set
     * @covers Desk\Relationship\Visitor\ResponseVisitor::get
     */
    public function testSetAndGet()
    {
        $visitor = $this->mock(array('set', 'get'));
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');

        $visitor->set($command, 'foo', 'bar');
        $this->assertSame('bar', $visitor->get($command, 'foo'));
    }

    /**
     * @covers Desk\Relationship\Visitor\ResponseVisitor::set
     */
    public function testSetMultipleItems()
    {
        $visitor = $this->mock(array('set', 'get'));
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');

        $visitor->set($command, 'foo', 'bar');
        $visitor->set($command, 'baz', 'qux');
        $this->assertSame('bar', $visitor->get($command, 'foo'));
        $this->assertSame('qux', $visitor->get($command, 'baz'));
    }

    /**
     * @covers Desk\Relationship\Visitor\ResponseVisitor::get
     */
    public function testGetBeforeSetReturnsNull()
    {
        $visitor = $this->mock('get');
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $this->assertNull($visitor->get($command, 'foo'));
    }

    /**
     * @covers Desk\Relationship\Visitor\ResponseVisitor::get
     */
    public function testGetAfterSetDifferentObjectReturnsNull()
    {
        $visitor = $this->mock(array('set', 'get'));
        $command1 = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $command2 = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');

        $visitor->set($command1, 'foo', 'bar');
        $this->assertNull($visitor->get($command2, 'foo'));
    }

    /**
     * @covers Desk\Relationship\Visitor\ResponseVisitor::get
     */
    public function testGetAfterSetDifferentKeyReturnsNull()
    {
        $visitor = $this->mock(array('set', 'get'));
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');

        $visitor->set($command, 'foo', 'bar');
        $this->assertNull($visitor->get($command, 'baz'));
    }

    /**
     * @covers Desk\Relationship\Visitor\ResponseVisitor::after
     */
    public function testAfterFunctionClearsStorage()
    {
        $visitor = $this->mock(array('set', 'get', 'after'));
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');

        $visitor->set($command, 'foo', 'bar');
        $this->assertSame('bar', $visitor->get($command, 'foo'));

        $visitor->after($command);
        $this->assertNull($visitor->get($command, 'foo'));
    }
}
