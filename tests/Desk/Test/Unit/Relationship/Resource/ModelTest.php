<?php

namespace Desk\Test\Unit\Relationship\Resource;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\UnitTestCase;

class ModelTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Resource\\Model';
    }

    /**
     * @covers Desk\Relationship\Resource\Model::__construct
     */
    public function testConstruct()
    {
        $model = new Model();
        $this->assertInstanceOf($this->getMockedClass(), $model);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::__construct
     */
    public function testConstructWithLinks()
    {
        $model = new Model(array('_links' => '$links'));
        $this->assertInstanceOf($this->getMockedClass(), $model);

        $links = $this->getPrivateProperty($model, 'links');
        $this->assertSame('$links', $links);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::__construct
     */
    public function testConstructWithEmbedded()
    {
        $model = new Model(array('_embedded' => '$embedded'));
        $this->assertInstanceOf($this->getMockedClass(), $model);

        $embedded = $this->getPrivateProperty($model, 'embedded');
        $this->assertSame('$embedded', $embedded);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::getLink
     */
    public function testGetLink()
    {
        $links = array('foo' => 'bar');
        $model = new Model(array('_links' => $links));

        $link = $model->getLink('foo');
        $this->assertSame('bar', $link);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::getLink
     * @expectedException Desk\Relationship\Exception\UnknownResourceException
     * @expectedExceptionMessage No related resource named 'baz'
     */
    public function testGetLinkInvalid()
    {
        $links = array('foo' => 'bar');
        $model = new Model(array('_links' => $links));
        $link = $model->getLink('baz');
    }

    /**
     * @covers Desk\Relationship\Resource\Model::getEmbedded
     */
    public function testGetEmbedded()
    {
        $embedded = array('foo' => 'bar');
        $model = new Model(array('_embedded' => $embedded));

        $resource = $model->getEmbedded('foo');
        $this->assertSame('bar', $resource);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::getEmbedded
     * @expectedException Desk\Relationship\Exception\UnknownResourceException
     * @expectedExceptionMessage No related resource named 'baz'
     */
    public function testGetEmbeddedInvalid()
    {
        $embedded = array('foo' => 'bar');
        $model = new Model(array('_embedded' => $embedded));
        $resource = $model->getEmbedded('baz');
    }

    /**
     * @covers Desk\Relationship\Resource\Model::hasLink
     */
    public function testHasLinkTrue()
    {
        $links = array('foo' => 'bar');
        $model = new Model(array('_links' => $links));

        $result = $model->hasLink('foo');
        $this->assertTrue($result);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::hasLink
     */
    public function testHasLinkFalse()
    {
        $links = array('foo' => 'bar');
        $model = new Model(array('_links' => $links));

        $result = $model->hasLink('baz');
        $this->assertFalse($result);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::hasEmbedded
     */
    public function testHasEmbeddedTrue()
    {
        $embedded = array('foo' => 'bar');
        $model = new Model(array('_embedded' => $embedded));

        $result = $model->hasEmbedded('foo');
        $this->assertTrue($result);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::hasEmbedded
     */
    public function testHasEmbeddedFalse()
    {
        $embedded = array('foo' => 'bar');
        $model = new Model(array('_embedded' => $embedded));

        $result = $model->hasEmbedded('baz');
        $this->assertFalse($result);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::getResource
     */
    public function testGetResourceEmbedded()
    {
        $embedded = array('foo' => 'bar');
        $model = new Model(array('_embedded' => $embedded));

        $resource = $model->getResource('foo');
        $this->assertSame('bar', $resource);
    }

    /**
     * @covers Desk\Relationship\Resource\Model::getResource
     */
    public function testGetResourceNotEmbedded()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\OperationCommand')
            ->shouldReceive('execute')
                ->andReturn('bar')
            ->getMock();

        $model = new Model(array('_links' => array('foo' => $command)));

        $resource = $model->getResource('foo');
        $this->assertSame('bar', $resource);
    }
}
