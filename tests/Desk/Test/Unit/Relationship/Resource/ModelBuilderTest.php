<?php

namespace Desk\Test\Unit\Relationship\Resource;

use Desk\Relationship\Resource\ModelBuilder;
use Desk\Test\Helper\UnitTestCase;

class ModelBuilderTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Resource\\ModelBuilder';
    }

    /**
     * @covers Desk\Relationship\Resource\ModelBuilder::__construct
     */
    public function testConstruct()
    {
        $factory = \Mockery::mock(
            'Desk\\Relationship\\Resource\\EmbeddedCommandFactoryInterface'
        );

        $visitors = \Mockery::mock(
            'Guzzle\Service\Command\LocationVisitor\VisitorFlyweight'
        );

        $builder = new ModelBuilder($factory, $visitors);

        $builderVisitors = $this->getPrivateProperty($builder, 'visitors');
        $builderFactory = $this->getPrivateProperty($builder, 'factory');
        $this->assertSame($visitors, $builderVisitors);
        $this->assertSame($factory, $builderFactory);
    }

    /**
     * @covers Desk\Relationship\Resource\ModelBuilder::createEmbeddedModel
     */
    public function testCreateEmbeddedModel()
    {
        $fooProperty = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getLocation')
                ->andReturn('json')
            ->shouldReceive('getName')
                ->andReturn('foo')
            ->getMock();

        $originalCommand = \Mockery::mock(
            'Guzzle\\Service\\Command\\CommandInterface'
        );

        $embeddedCommand = \Mockery::mock(
            'Desk\\Relationship\\Resource\\EmbeddedCommand'
        );

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getType')
                ->andReturn('object')
            ->shouldReceive('getProperties')
                ->andReturn(array('foo' => $fooProperty))
            ->getMock();

        $data = array('foo' => 'bar_raw');

        $factory = \Mockery::mock(
            'Desk\\Relationship\\Resource\\EmbeddedCommandFactoryInterface'
        );

        $factory
            ->shouldReceive('factory')
                ->with($originalCommand, $data)
                ->andReturn($embeddedCommand);

        $builder = $this->mock('createEmbeddedModel', array($factory))
            ->shouldReceive('process')
                ->with($embeddedCommand, $structure)
                ->andReturn(array('foo' => 'bar_processed'))
            ->getMock();

        $model = $builder->createEmbeddedModel(
            $originalCommand,
            $structure,
            $data
        );

        $this->assertInstanceOf('Desk\\Relationship\\Resource\\Model', $model);
        $this->assertSame('bar_processed', $model->get('foo'));
    }

    /**
     * @covers Desk\Relationship\Resource\ModelBuilder::createEmbeddedModel
     */
    public function testCreateEmbeddedModelWithExtendsChangesParameterName()
    {
        $fooProperty = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getLocation')
                ->andReturn('json')
            ->shouldReceive('getName')
                ->andReturn('foo')
            ->getMock();

        $originalCommand = \Mockery::mock(
            'Guzzle\\Service\\Command\\CommandInterface'
        );

        $embeddedCommand = \Mockery::mock(
            'Desk\\Relationship\\Resource\\EmbeddedCommand'
        );

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getType')
                ->andReturn('object')
            ->shouldReceive('getProperties')
                ->andReturn(array('foo' => $fooProperty))
            ->shouldReceive('setName')
                ->with('myOperationName')
            ->getMock();

        $structure->extends = 'myOperationName';

        $data = array('foo' => 'bar_raw');

        $factory = \Mockery::mock(
            'Desk\\Relationship\\Resource\\EmbeddedCommandFactoryInterface'
        );

        $factory
            ->shouldReceive('factory')
                ->with($originalCommand, $data)
                ->andReturn($embeddedCommand);

        $builder = $this->mock('createEmbeddedModel', array($factory))
            ->shouldReceive('process')
                ->with($embeddedCommand, $structure)
                ->andReturn(array('foo' => 'bar_processed'))
            ->getMock();

        $model = $builder->createEmbeddedModel(
            $originalCommand,
            $structure,
            $data
        );

        $this->assertInstanceOf('Desk\\Relationship\\Resource\\Model', $model);
        $this->assertSame('bar_processed', $model->get('foo'));
    }

    /**
     * @covers Desk\Relationship\Resource\ModelBuilder::createEmbeddedModel
     */
    public function testCreateEmbeddedModelWithEmbeddedArray()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getType')
                ->andReturn('array')
            ->getMock();

        $data = array(array('foo' => 'bar'), array('baz' => 'qux'));

        $builder = $this->mock('createEmbeddedModel')
            ->shouldReceive('createEmbeddedModelArray')
                ->with($command, $structure, $data)
                ->andReturn(array('return' => 'value'))
            ->getMock();

        $models = $builder->createEmbeddedModel($command, $structure, $data);

        $this->assertInternalType('array', $models);
        $this->assertSame(array('return' => 'value'), $models);
    }

    /**
     * @covers Desk\Relationship\Resource\ModelBuilder::createEmbeddedModelArray
     */
    public function testCreateEmbeddedModelArray()
    {
        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $items = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getItems')
                ->andReturn($items)
            ->getMock();

        $data = array(array('foo' => 'bar'), array('baz' => 'qux'));

        $builder = $this->mock('createEmbeddedModelArray')
            ->shouldReceive('createEmbeddedModel')
                ->with($command, $items, array('foo' => 'bar'))
                ->andReturn('$model1')
            ->shouldReceive('createEmbeddedModel')
                ->with($command, $items, array('baz' => 'qux'))
                ->andReturn('$model2')
            ->getMock();

        $models = $builder->createEmbeddedModelArray($command, $structure, $data);

        $this->assertInternalType('array', $models);
        $this->assertSame(array('$model1', '$model2'), $models);
    }

    /**
     * @covers Desk\Relationship\Resource\ModelBuilder::process
     * @covers Desk\Relationship\Resource\ModelBuilder::addVisitor
     */
    public function testProcess()
    {
        $data = array('foo' => 'bar', 'baz' => 'qux');

        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response');

        $parameterClass = 'Guzzle\\Service\\Description\\Parameter';
        $property = \Mockery::mock($parameterClass)
            ->shouldReceive('getLocation')
                ->andReturn('fooLocation')
            ->getMock();

        $additional = \Mockery::mock($parameterClass)
            ->shouldReceive('getLocation')
                ->andReturn('barLocation')
            ->shouldReceive('setName')
                ->with('baz')
            ->shouldReceive('setName')
                ->with(null)
            ->getMock();

        $schema = \Mockery::mock($parameterClass)
            ->shouldReceive('getProperties')
                ->andReturn(array('foo' => $property))
            ->shouldReceive('getAdditionalProperties')
                ->andReturn($additional)
            ->shouldReceive('getProperty')
                ->with('foo')
                ->andReturn($property)
            ->shouldReceive('getProperty')
                ->with('baz')
                ->andReturn(null)
            ->getMock();

        $embeddedCommandClass = 'Desk\\Relationship\\Resource\\EmbeddedCommand';
        $embeddedCommand = \Mockery::mock($embeddedCommandClass)
            ->shouldReceive('getResponse')
                ->andReturn($response)
            ->getMock();

        $visitor = 'Guzzle\\Service\\Command\\LocationVisitor\\Response\\ResponseVisitorInterface';

        $fooVisitor = \Mockery::mock($visitor)
            ->shouldReceive('before')
                ->with($embeddedCommand, array())
                ->atLeast()->once()
                ->ordered()
            ->shouldReceive('visit')
                ->with($embeddedCommand, $response, $property, $data)
                ->atLeast()->once()
                ->ordered()
            ->shouldReceive('after')
                ->with($embeddedCommand)
                ->atLeast()->once()
                ->ordered()
            ->getMock();

        $barVisitor = \Mockery::mock($visitor)
            ->shouldReceive('before')
                ->with(
                    $embeddedCommand,
                    \Mockery::on(
                        function (&$result) use ($data) {
                            if (is_array($result) && !count($result)) {
                                $result = $data;
                                return true;
                            }

                            return false;
                        }
                    )
                )
                ->atLeast()->once()
                ->ordered()
            ->shouldReceive('visit')
                ->with($embeddedCommand, $response, $additional, $data)
                ->atLeast()->once()
                ->ordered()
            ->shouldReceive('after')
                ->with($embeddedCommand)
                ->atLeast()->once()
                ->ordered()
            ->getMock();

        $class = 'Guzzle\\Service\\Command\\LocationVisitor\\VisitorFlyweight';
        $visitors = \Mockery::mock($class)
            ->shouldReceive('getResponseVisitor')
                ->with('fooLocation')
                ->andReturn($fooVisitor)
            ->shouldReceive('getResponseVisitor')
                ->with('barLocation')
                ->andReturn($barVisitor)
            ->getMock();

        $builder = $this->mock('process', array(null, $visitors));
        $result = $builder->process($embeddedCommand, $schema, $data);

        $this->assertSame($data, $result);
    }
}
