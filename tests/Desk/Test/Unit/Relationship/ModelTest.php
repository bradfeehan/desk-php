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
                ->with(
                    'myLink',
                    array('link' => 'content'),
                    array('the' => 'description')
                )
                ->andReturn('returnValue')
            ->getMock();

        $data = array(
            '_links' => array(
                'myLink' => array('link' => 'content'),
            ),
        );

        $model = $this->mock('getLink', array($builder, $data))
            ->shouldReceive('getLinkDescription')
                ->with('myLink')
                ->andReturn(array('the' => 'description'))
            ->getMock();

        $result = $model->getLink('myLink');
        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Model::getLink
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Link 'barLink' not found on this model
     */
    public function testGetLinkInvalid()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');
        $data = array(
            '_links' => array(
                'fooLink' => array('link' => 'content'),
            ),
        );

        $model = $this->mock('getLink', array($builder, $data))
            ->shouldReceive('getLinkDescription')
                ->with('barLink')
                ->andReturn(array('the' => 'description'))
            ->getMock();

        $model->getLink('barLink');
    }

    /**
     * @covers Desk\Relationship\Model::getEmbedded
     */
    public function testGetEmbedded()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface')
            ->shouldReceive('createModelFromEmbedded')
                ->with(
                    'myEmbedded',
                    array('embedded' => 'content'),
                    array('the' => 'description')
                )
                ->andReturn('returnValue')
            ->getMock();

        $data = array(
            '_embedded' => array(
                'myEmbedded' => array('embedded' => 'content'),
            ),
        );

        $model = $this->mock('getEmbedded', array($builder, $data))
            ->shouldReceive('getLinkDescription')
                ->with('myEmbedded')
                ->andReturn(array('the' => 'description'))
            ->getMock();

        $result = $model->getEmbedded('myEmbedded');
        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Model::getEmbedded
     * @expectedException Desk\Exception\UnexpectedValueException
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

        $model = $this->mock('getEmbedded', array($builder, $data))
            ->shouldReceive('getLinkDescription')
                ->with('barEmbedded')
                ->andReturn(array('the' => 'description'))
            ->getMock();

        $model->getEmbedded('barEmbedded');
    }

    /**
     * @covers Desk\Relationship\Model::getLinkDescription
     */
    public function testGetLinkDescription()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');

        $fooLink = array(
            'operation' => 'FooOperation',
            'pattern' => '#/path/to/foo/(?P<id>\\d+)$#',
        );

        $barLink = array(
            'operation' => 'BarOperation',
            'pattern' => '#/path/to/bar/(?P<id>\\d+)$#',
        );

        $links = array(
            'fooLink' => $fooLink,
            'barLink' => $barLink,
        );

        $model = $this->mock('getLinkDescription', array($builder));
        $model
            ->shouldReceive('getStructure->getData')
                ->with('links')
                ->andReturn($links);

        $description = $model->getLinkDescription('barLink');

        $this->assertSame($barLink, $description);
    }

    /**
     * @covers Desk\Relationship\Model::getLinkDescription
     * @expectedException Desk\Exception\InvalidArgumentException
     * @expectedExceptionMessage Missing link description for link 'barLink'
     */
    public function testGetLinkDescriptionInvalid()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilderInterface');

        $fooLink = array(
            'operation' => 'FooOperation',
            'pattern' => '#/path/to/foo/(?P<id>\\d+)$#',
        );

        $links = array(
            'fooLink' => $fooLink,
        );

        $model = $this->mock('getLinkDescription', array($builder));
        $model
            ->shouldReceive('getStructure->getData')
                ->with('links')
                ->andReturn($links);

        $model->getLinkDescription('barLink');
    }
}
