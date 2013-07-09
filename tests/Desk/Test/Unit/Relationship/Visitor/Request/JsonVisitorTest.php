<?php

namespace Desk\Test\Unit\Relationship\Visitor\Request;

use Desk\Relationship\Visitor\Request\JsonVisitor;
use Desk\Test\Helper\UnitTestCase;

class JsonVisitorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Visitor\\Request\\JsonVisitor';
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::__construct
     */
    public function testConstruct()
    {
        $class = 'Guzzle\\Service\\Command\\LocationVisitor\\Request\\RequestVisitorInterface';
        $component = \Mockery::mock($class);
        $visitor = new JsonVisitor($component);
        $this->assertInstanceOf($this->getMockedClass(), $visitor);

        $visitorComponent = $visitor->getDecoratedComponent();
        $this->assertSame($component, $visitorComponent);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::__construct
     */
    public function testConstructWithoutArgs()
    {
        $visitor = new JsonVisitor();
        $this->assertInstanceOf($this->getMockedClass(), $visitor);

        $visitorComponent = $visitor->getDecoratedComponent();
        $class = 'Guzzle\\Service\\Command\\LocationVisitor\\Request\\RequestVisitorInterface';
        $this->assertInstanceOf($class, $visitorComponent);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::visit
     */
    public function testVisitJsonLocation()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $param = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getLocation')
                ->andReturn('json')
            ->getMock();
        $value = \Mockery::mock();

        $visitor = $this->mock('visit');
        $visitor
            ->shouldReceive('getDecoratedComponent->visit')
                ->with($command, $request, $param, $value)
                ->andReturn('$returnValue')
            ->getMock();

        $result = $visitor->visit($command, $request, $param, $value);
        $this->assertSame('$returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::visit
     */
    public function testVisitLinksLocation()
    {
        $linkParam = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');
        $linkValue = array('foo' => 'bar');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');
        $param = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getLocation')
                ->andReturn('links')
            ->getMock();
        $value = \Mockery::mock();

        $visitor = $this->mock('visit');
        $visitor
            ->shouldReceive('createLinkParameter')
                ->andReturn($linkParam)
            ->shouldReceive('createLinkValue')
                ->andReturn($linkValue)
            ->shouldReceive('getDecoratedComponent->visit')
                ->with($command, $request, $linkParam, $linkValue)
                ->andReturn('$returnValue');

        $result = $visitor->visit($command, $request, $param, $value);
        $this->assertSame('$returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::createLinkParameter
     */
    public function testCreateLinkParameter()
    {
        $parameter = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getName')
                ->andReturn('$name')
            ->shouldReceive('getDescription')
                ->andReturn('$description')
            ->shouldReceive('getRequired')
                ->andReturn(true)
            ->shouldReceive('getSentAs')
                ->andReturn('$sentAs')
            ->getMock();

        $visitor = $this->mock('createLinkParameter');
        $result = $visitor->createLinkParameter($parameter);

        $this->assertInstanceOf('Guzzle\\Service\\Description\\Parameter', $result);
        $this->assertSame('$name', $result->getName());
        $this->assertSame('$description', $result->getDescription());
        $this->assertSame(true, $result->getRequired());
        $this->assertSame('$sentAs', $result->getSentAs());

        $properties = $result->getProperties();
        $this->assertSame(2, count($properties));

        $class = $properties['class'];
        $this->assertSame('string', $class->getType());
        $this->assertSame(true, $class->getRequired());
        $this->assertSame('/^[a-z_]+$/', $class->getPattern());

        $href = $properties['href'];
        $this->assertSame('string', $href->getType());
        $this->assertSame(true, $href->getRequired());
        $this->assertSame('#^/api/v2/#', $href->getPattern());
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::createLinkValue
     */
    public function testCreateLinkValue()
    {
        $value = 1;

        $parameter = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('class')
                ->andReturn('$class')
            ->shouldReceive('getData')
                ->with('href')
                ->andReturn('/foo/bar/{value}/test')
            ->shouldReceive('getValue')
                ->with($value)
                ->andReturn('$value')
            ->getMock();

        $visitor = $this->mock('createLinkValue');
        $result = $visitor->createLinkValue($parameter, $value);

        $expected = array(
            'class' => '$class',
            'href' => '/foo/bar/$value/test',
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::after
     */
    public function testAfter()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');

        $component = \Mockery::mock()
            ->shouldReceive('visit')
            ->getMock();

        $visitor = $this->mock('after');
        $visitor
            ->shouldReceive('getDecoratedComponent->after')
                ->with($command, $request)
                ->andReturn('$returnValue');

        $result = $visitor->after($command, $request);
        $this->assertSame('$returnValue', $result);
    }
}
