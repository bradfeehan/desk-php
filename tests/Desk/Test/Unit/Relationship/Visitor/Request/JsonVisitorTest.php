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

        $visitor = $this->mock('visit')
            ->shouldReceive('addLinkParam')
                ->with($command, $param)
                ->once()
            ->shouldReceive('addLinkValue')
                ->with($command, $param, $value)
                ->once()
            ->getMock();

        $result = $visitor->visit($command, $request, $param, $value);
        $this->assertNull($result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::addLinkParam
     */
    public function testAddLinkParam()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
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

        $fooParam = array(
            'name' => '$name',
            'description' => '$description',
            'required' => true,
            'sentAs' => '$sentAs',
            'type' => 'object',
            'properties' => array(
                'class' => array(
                    'type' => 'string',
                    'required' => true,
                    'pattern' => '/^[a-z_]+$/'
                ),
                'href' => array(
                    'type' => 'string',
                    'required' => true,
                    'pattern' => '#^/api/v2/#'
                ),
            ),
        );

        $params = \Mockery::mock('SplObjectStorage')
            ->shouldReceive('offsetExists')
                ->with($command)
                ->andReturn(true)
            ->shouldReceive('offsetGet')
                ->with($command)
                ->andReturn(array('foo' => 'bar'))
            ->shouldReceive('offsetSet')
                ->with($command, array('foo' => 'bar', '$name' => $fooParam))
            ->getMock();

        $visitor = $this->mock('addLinkParam');
        $this->setPrivateProperty($visitor, 'params', $params);

        $result = $visitor->addLinkParam($command, $parameter);
        $this->assertNull($result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::addLinkValue
     */
    public function testAddLinkValue()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $value = 123;
        $parameter = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getName')
                ->andReturn('$name')
            ->shouldReceive('getData')
                ->with('class')
                ->andReturn('$class')
            ->shouldReceive('getData')
                ->with('href')
                ->andReturn('$href_{value}')
            ->shouldReceive('getValue')
                ->with($value)
                ->andReturn(456)
            ->getMock();

        $fooValue = array(
            'class' => '$class',
            'href' => '$href_456',
        );

        $values = \Mockery::mock('SplObjectStorage')
            ->shouldReceive('offsetExists')
                ->with($command)
                ->andReturn(true)
            ->shouldReceive('offsetGet')
                ->with($command)
                ->andReturn(array('bar' => 'baz'))
            ->shouldReceive('offsetSet')
                ->with($command, array('bar' => 'baz', '$name' => $fooValue))
            ->getMock();

        $visitor = $this->mock('addLinkValue');
        $this->setPrivateProperty($visitor, 'values', $values);

        $result = $visitor->addLinkValue($command, $parameter, $value);
        $this->assertNull($result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::getLinksParameter
     */
    public function testGetLinksParameter()
    {
        $command = \Mockery::mock('Guzzle\Service\Command\CommandInterface');
        $params = \Mockery::mock('SplObjectStorage')
            ->shouldReceive('offsetExists')
                ->with($command)
                ->andReturn(true)
            ->shouldReceive('offsetGet')
                ->with($command)
                ->andReturn(array('foo' => array('type' => 'string')))
            ->shouldReceive('offsetUnset')
                ->with($command)
                ->once()
            ->getMock();

        $visitor = $this->mock('getLinksParameter');
        $this->setPrivateProperty($visitor, 'params', $params);

        $result = $visitor->getLinksParameter($command);
        $this->assertInstanceOf('Guzzle\\Service\\Description\\Parameter', $result);

        $this->assertSame('_links', $result->getName());
        $this->assertSame('json', $result->getLocation());
        $this->assertSame('object', $result->getType());

        $properties = $result->getProperties();
        $this->assertSame(1, count($properties));

        $property = $properties['foo'];
        $this->assertInstanceOf('Guzzle\\Service\\Description\\Parameter', $property);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::getLinksValues
     */
    public function testGetLinksValues()
    {
        $command = \Mockery::mock('Guzzle\Service\Command\CommandInterface');
        $values = \Mockery::mock('SplObjectStorage')
            ->shouldReceive('offsetExists')
                ->with($command)
                ->andReturn(true)
            ->shouldReceive('offsetGet')
                ->with($command)
                ->andReturn(array('foo' => 'bar'))
            ->shouldReceive('offsetUnset')
                ->with($command)
                ->once()
            ->getMock();

        $visitor = $this->mock('getLinksValues');
        $this->setPrivateProperty($visitor, 'values', $values);

        $result = $visitor->getLinksValues($command);
        $this->assertSame(array('foo' => 'bar'), $result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::getLinksValues
     */
    public function testGetLinksValuesWithNoValues()
    {
        $command = \Mockery::mock('Guzzle\Service\Command\CommandInterface');
        $values = \Mockery::mock('SplObjectStorage')
            ->shouldReceive('offsetExists')
                ->with($command)
                ->andReturn(false)
            ->getMock();

        $visitor = $this->mock('getLinksValues');
        $this->setPrivateProperty($visitor, 'values', $values);

        $result = $visitor->getLinksValues($command);
        $this->assertNull($result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::after
     */
    public function testAfter()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');

        $param = \Mockery::mock();
        $values = \Mockery::mock();

        $visitor = $this->mock('after')
            ->shouldReceive('getLinksValues')
                ->with($command)
                ->andReturn($values)
            ->shouldReceive('getLinksParameter')
                ->with($command)
                ->andReturn($param)
            ->getMock();

        $visitor
            ->shouldReceive('getDecoratedComponent->after')
                ->once()
                ->with($command, $request)
                ->andReturn('$returnValue')
            ->shouldReceive('visit')
                ->once()
                ->with($command, $request, $param, $values);

        $result = $visitor->after($command, $request);
        $this->assertSame('$returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Visitor\Request\JsonVisitor::after
     */
    public function testAfterWithNoLinksData()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $request = \Mockery::mock('Guzzle\\Http\\Message\\RequestInterface');

        $visitor = $this->mock('after')
            ->shouldReceive('getLinksValues')
                ->with($command)
                ->andReturn(null)
            ->getMock();

        $visitor
            ->shouldReceive('getDecoratedComponent->after')
                ->once()
                ->with($command, $request)
                ->andReturn('$returnValue');

        $result = $visitor->after($command, $request);
        $this->assertSame('$returnValue', $result);
    }
}
