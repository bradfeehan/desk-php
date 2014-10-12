<?php

namespace Desk\Test\Unit\Relationship\Visitor\Response;

use Desk\Relationship\Visitor\Response\EmbeddedVisitor;
use Desk\Test\Helper\UnitTestCase;

class EmbeddedVisitorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Visitor\\Response\\EmbeddedVisitor';
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\EmbeddedVisitor::__construct
     */
    public function testConstruct()
    {
        $builderClass = 'Desk\\Relationship\\Resource\\ModelBuilderInterface';
        $builder = \Mockery::mock($builderClass);

        $visitor = new EmbeddedVisitor($builder);
        $visitorBuilder = $this->getPrivateProperty($visitor, 'builder');
        $this->assertSame($builder, $visitorBuilder);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\AbstractVisitor::before
     * @covers Desk\Relationship\Visitor\Response\EmbeddedVisitor::getFieldName
     * @covers Desk\Relationship\Visitor\Response\EmbeddedVisitor::getOutputFieldName
     */
    public function testBefore()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $command
            ->shouldReceive('getResponse->json')
                ->andReturn(array('foo' => 'bar'));

        $result = array();

        $visitor = $this->mock('before');
        $visitor->before($command, $result);

        $this->assertArrayHasKey('_embedded', $result);
        $this->assertSame(array(), $result['_embedded']);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\AbstractVisitor::before
     * @covers Desk\Relationship\Visitor\Response\EmbeddedVisitor::getFieldName
     * @covers Desk\Relationship\Visitor\Response\EmbeddedVisitor::getOutputFieldName
     */
    public function testBeforeWithEmbeddedModel()
    {
        $embedded = array('myModel' => array('bar' => 'baz'));
        $data = array('foo' => 'bar', '_embedded' => $embedded);

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $command
            ->shouldReceive('getResponse->json')
                ->andReturn($data);

        $result = array();

        $visitor = $this->mock('before')
            ->shouldReceive('set')
                ->with($command, 'embedded', $embedded)
                ->once()
            ->getMock();

        $visitor->before($command, $result);

        $this->assertArrayHasKey('_embedded', $result);
        $this->assertSame(array(), $result['_embedded']);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\AbstractVisitor::visit
     * @covers Desk\Relationship\Visitor\Response\EmbeddedVisitor::createResourceFromData
     */
    public function testVisit()
    {
        $data = array('foo' => 'bar');
        $resources = array('fooWire' => $data);

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response');
        $parameter = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getName')
                ->andReturn('fooName')
            ->shouldReceive('getWireName')
                ->andReturn('fooWire')
            ->getMock();

        $builderClass = 'Desk\\Relationship\\Resource\\ModelBuilderInterface';
        $builder = \Mockery::mock($builderClass)
            ->shouldReceive('createEmbeddedModel')
                ->with($command, $parameter, $data)
                ->andReturn('$result')
            ->getMock();

        $visitor = $this->mock('visit', array($builder))
            ->shouldReceive('get')
                ->with($command, 'embedded')
                ->andReturn($resources)
            ->getMock();

        $result = array('_embedded' => array());

        $visitor->visit($command, $response, $parameter, $result);

        $this->assertArrayHasKey('_embedded', $result);
        $this->assertArrayHasKey('fooName', $result['_embedded']);
        $this->assertSame('$result', $result['_embedded']['fooName']);
    }
}
