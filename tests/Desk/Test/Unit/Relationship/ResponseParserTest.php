<?php

namespace Desk\Test\Unit\Relationship;

use Desk\Relationship\ResponseParser;
use Desk\Test\Helper\UnitTestCase;
use Guzzle\Service\Command\AbstractCommand;
use ReflectionClass;
use ReflectionObject;

class ResponseParserTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\ResponseParser';
    }

    /**
     * @covers Desk\Relationship\ResponseParser::getInstance
     */
    public function testGetInstance()
    {
        // Clear instance to start with, so we hit the code path where
        // it is unset
        $class = new ReflectionClass('Desk\\Relationship\\ResponseParser');
        $property = $class->getProperty('relationshipInstance');
        $property->setAccessible(true);
        $property->setValue(null);

        $instance = ResponseParser::getInstance();
        $this->assertInstanceOf($this->getMockedClass(), $instance);
    }

    /**
     * @covers Desk\Relationship\ResponseParser::setResourceBuilder
     */
    public function testSetResourceBuilder()
    {
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilder');

        $parser = $this->mock();
        $parser->setResourceBuilder($builder);

        $parserBuilder = $this->getPrivateProperty($parser, 'builder');
        $this->assertSame($builder, $parserBuilder);
    }

    /**
     * @covers Desk\Relationship\ResponseParser::handleParsing
     */
    public function testHandleParsing()
    {
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getProperties')
                ->andReturn(array())
            ->shouldReceive('getAdditionalProperties')
                ->andReturn(array())
            ->getMock();

        $operation = \Mockery::mock()
            ->shouldReceive('getResponseClass')
                ->andReturn('myModelName')
            ->getMock();

        $operation
            ->shouldReceive('getServiceDescription->getModel')
                ->with('myModelName')
                ->andReturn($structure);

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand')
            ->shouldReceive('getOperation')
                ->andReturn($operation)
            ->getMock();

        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response');
        $contentType = 'content_type';
        $builder = \Mockery::mock('Desk\\Relationship\\ResourceBuilder');

        $parser = $this->mock(array('responseTypeIsModel', 'createClass'));
        $parser
            ->shouldReceive('responseTypeIsModel')
                ->with($command)
                ->andReturn(true);
        $parser
            ->shouldReceive('createClass')
                ->with(
                    'Desk\\Relationship\\Model',
                    array($builder, array(), $structure)
                )
                ->andReturn('return_value');

        $parser->setResourceBuilder($builder);

        $reflectionParser = new ReflectionObject($parser);
        $handleParsing = $reflectionParser->getMethod('handleParsing');
        $handleParsing->setAccessible(true);
        $result = $handleParsing->invoke($parser, $command, $response, $contentType);

        $this->assertSame('return_value', $result);
    }

    /**
     * @covers Desk\Relationship\ResponseParser::handleParsing
     */
    public function testHandleParsingWithClassResponseType()
    {
        $operation = \Mockery::mock('Guzzle\\Service\\Description\\Operation');
        $operation
            ->shouldReceive('getResponseType')
                ->andReturn('model')
            ->shouldReceive('getResponseClass')
                ->andReturn('myModel')
            ->shouldReceive('getServiceDescription->getModel')
                ->with('myModel');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand')
            ->shouldReceive('getOperation')
                ->andReturn($operation)
            ->getMock();

        $response = \Mockery::mock('Guzzle\\Http\\Message\\Response')
            ->shouldReceive('getBody')
                ->andReturn('{"foo": "bar"}')
            ->getMock();

        $contentType = 'content_type';

        $parser = $this->mock();

        $reflectionParser = new ReflectionObject($parser);
        $handleParsing = $reflectionParser->getMethod('handleParsing');
        $handleParsing->setAccessible(true);
        $result = $handleParsing->invoke($parser, $command, $response, $contentType);

        $this->assertSame($response, $result);
    }

    /**
     * @covers Desk\Relationship\ResponseParser::createClass
     */
    public function testCreateClass()
    {
        $factory = $this->mock();
        $result = $factory->createClass('SplObjectStorage');
        $this->assertInstanceOf('SplObjectStorage', $result);
    }

    /**
     * @covers Desk\Relationship\ResponseParser::responseTypeIsModel
     */
    public function testResponseTypeIsModelTrue()
    {
        $description = \Mockery::mock('Guzzle\\Service\\Description\\ServiceDescription')
            ->shouldReceive('hasModel')
                ->with('myModel')
                ->andReturn(true)
            ->getMock();

        $operation = \Mockery::mock('Guzzle\\Service\\Description\\Operation')
            ->shouldReceive('getServiceDescription')
                ->andReturn($description)
            ->shouldReceive('getResponseClass')
                ->andReturn('myModel')
            ->shouldReceive('getResponseType')
                ->andReturn('model')
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand')
            ->shouldReceive('getOperation')
                ->andReturn($operation)
            ->shouldReceive('get')
                ->with(AbstractCommand::RESPONSE_PROCESSING)
                ->andReturn('model')
            ->getMock();

        $parser = $this->mock();
        $this->assertTrue($parser->responseTypeIsModel($command));
    }
    /**
     * @covers Desk\Relationship\ResponseParser::responseTypeIsModel
     */
    public function testResponseTypeIsModelFalse()
    {
        $operation = \Mockery::mock('Guzzle\\Service\\Description\\Operation')
            ->shouldReceive('getServiceDescription')
                ->andReturn('$description')
            ->shouldReceive('getResponseType')
                ->andReturn('definitely not "model"')
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand')
            ->shouldReceive('getOperation')
                ->andReturn($operation)
            ->shouldReceive('get')
                ->with(AbstractCommand::RESPONSE_PROCESSING)
            ->getMock();

        $parser = $this->mock();
        $this->assertFalse($parser->responseTypeIsModel($command));
    }
}
