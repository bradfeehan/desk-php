<?php

namespace Desk\Test\Unit\Common;

use Desk\Common\Decorator;
use Desk\Test\Helper\UnitTestCase;

class DecoratorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Common\\Decorator';
    }

    /**
     * @covers Desk\Common\Decorator::__construct
     */
    public function testConstruct()
    {
        $component = \Mockery::mock();
        $decorator = new Decorator($component);

        $decoratorComponent = $this->getPrivateProperty($decorator, 'component');
        $this->assertSame($component, $decoratorComponent);
    }

    /**
     * @covers Desk\Common\Decorator::__construct
     * @expectedException Desk\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot decorate array
     */
    public function testConstructInvalid()
    {
        $component = array(); // non-object
        $decorator = new Decorator(array($component));
    }

    /**
     * @covers Desk\Common\Decorator::getDecoratedComponent
     */
    public function testGetDecoratedComponent()
    {
        $component = \Mockery::mock();
        $decorator = $this->mock('getDecoratedComponent');
        $this->setPrivateProperty($decorator, 'component', $component);

        $result = $decorator->getDecoratedComponent();
        $this->assertSame($component, $result);
    }

    /**
     * @covers Desk\Common\Decorator::__call
     */
    public function testCall()
    {
        $component = \Mockery::mock()
            ->shouldReceive('foo')
                ->with('bar')
                ->andReturn('baz')
            ->getMock();

        $decorator = new Decorator($component);

        $result = $decorator->foo('bar');
        $this->assertSame('baz', $result);
    }

    /**
     * @covers Desk\Common\Decorator::__call
     * @expectedException Desk\Exception\BadMethodCallException
     * @expectedExceptionMessage method Desk\Common\Decorator::foo() (not defined on decorator or decorated stdClass)
     */
    public function testCallInvalid()
    {
        $component = new \stdClass();
        $decorator = new Decorator($component);

        $result = $decorator->foo('bar');
        $this->assertSame('baz', $result);
    }
}
