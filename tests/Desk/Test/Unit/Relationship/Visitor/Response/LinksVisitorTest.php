<?php

namespace Desk\Test\Unit\Relationship\Visitor\Response;

use Desk\Relationship\Visitor\Response\LinksVisitor;
use Desk\Test\Helper\UnitTestCase;

class LinksVisitorTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Visitor\\Response\\LinksVisitor';
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::__construct
     */
    public function testConstruct()
    {
        $builderClass = 'Desk\\Relationship\\Resource\\CommandBuilder';
        $builder = \Mockery::mock($builderClass);
        $visitor = new LinksVisitor($builder);

        $visitorBuilder = $this->getPrivateProperty($visitor, 'builder');
        $this->assertSame($builder, $visitorBuilder);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::__construct
     */
    public function testConstructWithoutBuilder()
    {
        $visitor = new LinksVisitor();
        $builder = $this->getPrivateProperty($visitor, 'builder');

        $builderClass = 'Desk\\Relationship\\Resource\\CommandBuilder';
        $this->assertInstanceOf($builderClass, $builder);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\AbstractVisitor::before
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::getFieldName
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::getOutputFieldName
     */
    public function testBefore()
    {
        $links = array('foo' => 'bar');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $command
            ->shouldReceive('getResponse->json')
                ->andReturn(array('abc' => 'def', '_links' => $links));

        $visitor = $this->mock('before')
            ->shouldReceive('set')
                ->with($command, 'links', $links)
                ->once()
            ->getMock();

        $result = array();

        $visitor->before($command, $result);
        $this->assertSame(array(), $result['_links']);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\AbstractVisitor::before
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::getFieldName
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::getOutputFieldName
     */
    public function testBeforeWithNoLinks()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $command
            ->shouldReceive('getResponse->json')
                ->andReturn(array('foo' => 'bar'));

        $result = array();

        $visitor = $this->mock('before');
        $visitor->before($command, $result);

        $this->assertArrayHasKey('_links', $result);
        $this->assertSame(array(), $result['_links']);
    }

    /**
     * @covers Desk\Relationship\Visitor\Response\AbstractVisitor::visit
     * @covers Desk\Relationship\Visitor\Response\LinksVisitor::createResourceFromData
     */
    public function testVisit()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response');
        $param = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getName')
                ->andReturn('theName')
            ->shouldReceive('getWireName')
                ->andReturn('theWireName')
            ->getMock();

        $link = array('foo' => 'bar');
        $links = array('theWireName' => $link);
        $value = array('_links' => array());

        $builderClass = 'Desk\\Relationship\\Resource\\CommandBuilder';
        $builder = \Mockery::mock($builderClass)
            ->shouldReceive('createLinkCommand')
                ->with($command, $param, $link)
                ->andReturn('$linkCommand')
            ->getMock();

        $visitor = $this->mock('visit', array($builder))
            ->shouldReceive('get')
                ->with($command, 'links')
                ->andReturn($links)
            ->getMock();

        $visitor->visit($command, $response, $param, $value);

        $expected = array('theName' => '$linkCommand');
        $this->assertArrayHasKey('_links', $value);
        $this->assertSame($expected, $value['_links']);
    }
}
