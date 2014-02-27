<?php

namespace Desk\Test\Unit\Relationship\Visitor;

use Desk\Test\Helper\UnitTestCase;

class DecoratedRequestVisitorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Visitor\\DecoratedRequestVisitor';
    }

    /**
     * @covers Desk\Relationship\Visitor\DecoratedRequestVisitor::__construct
     */
    public function testConstruct()
    {
        $className = 'Guzzle\\Service\\Command\\LocationVisitor\\Request\\RequestVisitorInterface';
        $component = \Mockery::mock($className);
        $decorator = $this->mock(array(), array($component));

        $decoratorComponent = $this->getPrivateProperty($decorator, 'component');
        $this->assertSame($component, $decoratorComponent);
    }
}
