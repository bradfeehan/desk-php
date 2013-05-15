<?php

namespace Desk\Test\Unit\Relationship;

use Desk\Relationship\Model;
use Desk\Test\Helper\UnitTestCase;

class ModelTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Model';
    }

    /**
     * @covers Desk\Relationship\Model::__construct
     */
    public function testConstruct()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');
        $data = array('foo' => 'bar');
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $model = new Model($builder, $data, $structure);

        $this->assertSame($data, $model->toArray());
        $this->assertSame($structure, $model->getStructure());

        $actualBuilder = $this->getPrivateProperty($model, 'builder');
        $this->assertSame($builder, $actualBuilder);
    }

    /**
     * @covers Desk\Relationship\Model::__construct
     */
    public function testConstructWithLinks()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');
        $data = array(
            'foo' => 'bar',
            '_links' => array(
                'baz' => 'qux',
            ),
        );

        $model = new Model($builder, $data);

        $links = $this->getPrivateProperty($model, 'links');
        $this->assertSame(array('baz' => 'qux'), $links);
    }

    /**
     * @covers Desk\Relationship\Model::__construct
     */
    public function testConstructWithEmbedded()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');
        $data = array(
            'foo' => 'bar',
            '_embedded' => array(
                'baz' => 'qux',
            ),
        );

        $model = new Model($builder, $data);

        $embedded = $this->getPrivateProperty($model, 'embedded');
        $this->assertSame(array('baz' => 'qux'), $embedded);
    }

    /**
     * @covers Desk\Relationship\Model::getLink
     */
    public function testGetLink()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface')
            ->shouldReceive('createCommandFromLink')
                ->with(array('link' => 'content'))
                ->andReturn('returnValue')
            ->getMock();

        $data = array(
            '_links' => array(
                'myLink' => array('link' => 'content'),
            ),
        );

        $model = new Model($builder, $data);

        $result = $model->getLink('myLink');
        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Model::getLink
     * @expectedException Desk\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown link 'barLink'
     */
    public function testGetLinkInvalid()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');
        $data = array(
            '_links' => array(
                'fooLink' => array('link' => 'content'),
            ),
        );

        $model = new Model($builder, $data);
        $model->getLink('barLink');
    }

    /**
     * @covers Desk\Relationship\Model::getEmbedded
     */
    public function testGetEmbedded()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface')
            ->shouldReceive('createModelFromEmbedded')
                ->with(array('embedded' => 'content'))
                ->andReturn('returnValue')
            ->getMock();

        $data = array(
            '_embedded' => array(
                'myEmbedded' => array('embedded' => 'content'),
            ),
        );

        $model = new Model($builder, $data);

        $result = $model->getEmbedded('myEmbedded');
        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Model::getEmbedded
     * @expectedException Desk\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown embedded resource 'barEmbedded'
     */
    public function testGetEmbeddedInvalid()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');
        $data = array(
            '_embedded' => array(
                'fooEmbedded' => array('embedded' => 'content'),
            ),
        );

        $model = new Model($builder, $data);
        $model->getEmbedded('barEmbedded');
    }
}
