<?php

namespace Desk\Test\Unit\Relationship;

use Desk\Relationship\ResourceBuilder;
use Desk\Test\Helper\UnitTestCase;

class ResourceBuilderTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\ResourceBuilder';
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::__construct
     */
    public function testConstruct()
    {
        $client = \Mockery::mock('Desk\\Client');
        $builder = new ResourceBuilder($client);

        $actualClient = $this->getPrivateProperty($builder, 'client');
        $this->assertSame($client, $actualClient);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createCommandFromLink
     */
    public function testCreateCommandFromLink()
    {
        $command = \Mockery::mock('Desk\\Command')
            ->shouldReceive('setUri')
                ->with('/path/to/resource')
            ->getMock();

        $link = array(
            'href' => '/path/to/resource',
            'class' => 'myClass',
        );

        $builder = $this->mock(array('validateLink', 'getCommandForDeskClass'))
            ->shouldReceive('validateLink')
                ->with($link)
            ->shouldReceive('getCommandForDeskClass')
                ->with('myClass')
                ->andReturn($command)
            ->getMock();

        $result = $builder->createCommandFromLink($link);
        $this->assertSame($command, $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createCommandFromLink
     * @expectedException Desk\Relationship\Exception\InvalidLinkFormatException
     * @expectedExceptionMessage Unknown linked resource class 'myClass'
     */
    public function testCreateCommandFromLinkInvalid()
    {
        $command = \Mockery::mock();
        $command
            ->shouldReceive('getOperation->setUri')
                ->with('/path/to/resource');

        $link = array(
            'href' => '/path/to/resource',
            'class' => 'myClass',
        );

        $builder = $this->mock(array('validateLink', 'getCommandForDeskClass'))
            ->shouldReceive('validateLink')
                ->with($link)
            ->shouldReceive('getCommandForDeskClass')
                ->with('myClass')
                ->andThrow('Desk\\Exception\\InvalidArgumentException')
            ->getMock();

        $result = $builder->createCommandFromLink($link);
        $this->assertSame($command, $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     */
    public function testCreateModelFromEmbedded()
    {
        $self = array(
            'class' => 'myClass',
            'href' => '/path/to/self',
        );

        $embedded = array(
            'foo' => 'bar',
            '_links' => array(
                'self' => $self,
            ),
        );

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $builder = $this->mock(array('validateLink', 'getModelForDeskClass'))
            ->shouldReceive('validateLink')
                ->with($self)
            ->shouldReceive('getModelForDeskClass')
                ->with('myClass')
                ->andReturn($structure)
            ->getMock();

        $model = $builder->createModelFromEmbedded($embedded);
        $this->assertSame(array('foo' => 'bar'), $model->toArray());
        $this->assertSame($structure, $model->getStructure());

        $modelBuilder = $this->getPrivateProperty($model, 'builder');
        $this->assertSame($builder, $modelBuilder);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     * @expectedException Desk\Relationship\Exception\InvalidEmbedFormatException
     * @expectedExceptionMessage format: missing expected '_links' element; missing
     */
    public function testCreateModelFromEmbeddedWithInvalidFormat()
    {
        $this->mock()->createModelFromEmbedded(array('foo' => 'bar'));
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     * @expectedException Desk\Relationship\Exception\InvalidLinkFormatException
     * @expectedExceptionMessage Invalid resource link format: missing expected 'class' element
     */
    public function testCreateModelFromEmbeddedWithInvalidSelfLinkFormat()
    {
        $embed = array(
            'foo' => 'bar',
            '_links' => array(
                'self' => array(
                    'href' => '/missing/desk/class',
                ),
            ),
        );

        $this->mock()->createModelFromEmbedded($embed);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     * @expectedException Desk\Relationship\Exception\InvalidEmbedFormatException
     * @expectedExceptionMessage Unknown embedded resource class 'myClass'
     */
    public function testCreateModelFromEmbeddedWithUnknownDeskClass()
    {
        $self = array(
            'class' => 'myClass',
            'href' => '/path/to/self',
        );

        $embedded = array(
            'foo' => 'bar',
            '_links' => array(
                'self' => $self,
            ),
        );

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $builder = $this->mock(array('validateLink', 'getModelForDeskClass'))
            ->shouldReceive('validateLink')
                ->with($self)
            ->shouldReceive('getModelForDeskClass')
                ->with('myClass')
                ->andThrow('Desk\\Exception\\InvalidArgumentException')
            ->getMock();

        $builder->createModelFromEmbedded($embedded);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLink
     */
    public function testValidateLink()
    {
        $builder = $this->mock();
        $builder->validateLink(
            array(
                'class' => 'myClass',
                'href' => 'path/to/foo',
            )
        );

        // to avoid "no assertions" error -- we are asserting that no
        // exception is thrown, but PHPUnit doesn't know that
        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLink
     * @expectedException Desk\Relationship\Exception\InvalidLinkFormatException
     */
    public function testValidateLinkInvalid()
    {
        $builder = $this->mock();
        $builder->validateLink(array('class' => 'missing href'));
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getCommandForDeskClass
     */
    public function testGetCommandForDeskClass()
    {
        $operations = array(
            \Mockery::mock('Guzzle\\Service\\Description\\Operation')
                ->shouldReceive('getData')
                    ->with('deskClass')
                    ->andReturn('fooClass')
                ->shouldReceive('getName')->never()
                ->getMock(),
            \Mockery::mock('Guzzle\\Service\\Description\\Operation')
                ->shouldReceive('getData')
                    ->with('deskClass')
                    ->andReturn('barClass')
                ->shouldReceive('getName')
                    ->andReturn('barOperation')
                ->getMock(),
        );

        $client = \Mockery::mock('Desk\\Client');
        $client
            ->shouldReceive('getCommand')
                ->with('barOperation')
                ->andReturn('returnValue')
            ->shouldReceive('getDescription->getOperations')
                ->andReturn($operations);

        $builder = new ResourceBuilder($client);

        $result = $builder->getCommandForDeskClass('barClass');
        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getCommandForDeskClass
     * @expectedException Desk\Exception\InvalidArgumentException
     */
    public function testGetCommandForDeskClassInvalid()
    {
        $operations = array(
            \Mockery::mock('Guzzle\\Service\\Description\\Operation')
                ->shouldReceive('getData')
                    ->with('deskClass')
                    ->andReturn('fooClass')
                ->getMock()
        );

        $client = \Mockery::mock('Desk\\Client');
        $client
            ->shouldReceive('getDescription->getOperations')
                ->andReturn($operations);

        $builder = new ResourceBuilder($client);
        $builder->getCommandForDeskClass('barClass');
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getModelForDeskClass
     */
    public function testGetModelForDeskClass()
    {
        $fooModel = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('deskClass')
                ->andReturn('fooClass')
            ->getMock();

        $barModel = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('deskClass')
                ->andReturn('barClass')
            ->getMock();

        $client = \Mockery::mock('Desk\\Client');
        $client->shouldReceive('getDescription->getModels')
            ->andReturn(array($fooModel, $barModel));

        $builder = new ResourceBuilder($client);

        $result = $builder->getModelForDeskClass('barClass');
        $this->assertSame($barModel, $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getModelForDeskClass
     * @expectedException Desk\Exception\InvalidArgumentException
     */
    public function testGetModelForDeskClassInvalid()
    {
        $models = array(
            \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
                ->shouldReceive('getData')
                    ->with('deskClass')
                    ->andReturn('fooClass')
                ->getMock(),
        );

        $client = \Mockery::mock('Desk\\Client');
        $client->shouldReceive('getDescription->getModels')
            ->andReturn($models);

        $builder = new ResourceBuilder($client);

        $builder->getModelForDeskClass('barClass');
    }
}
