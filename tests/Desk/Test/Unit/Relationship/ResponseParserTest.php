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
        $class = new ReflectionClass($this->getMockedClass());
        $property = $class->getProperty('relationshipInstance');
        $property->setAccessible(true);
        $property->setValue(null);

        $instance = ResponseParser::getInstance();
        $this->assertInstanceOf($this->getMockedClass(), $instance);
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

        $parser = $this->mock()
            ->shouldReceive('responseTypeIsModel')
                ->with($command)
                ->andReturn(true)
            ->shouldReceive('createClass')
                ->with(
                    'Desk\\Relationship\\Resource\\Model',
                    array(array(), $structure)
                )
                ->andReturn('return_value')
            ->getMock();

        $result = $this->invokeHandleParsing($parser, $command, $response, $contentType);

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

        $parser = $this->mock('handleParsing')
            ->shouldReceive('responseTypeIsModel')
                ->with($command)
                ->andReturn(false)
            ->getMock();

        $result = $this->invokeHandleParsing(
            $parser,
            $command,
            $response,
            $contentType
        );

        $this->assertSame($response, $result);
    }

    /**
     * @covers Desk\Relationship\ResponseParser::createClass
     */
    public function testCreateClass()
    {
        $factory = $this->mock('createClass');
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

        $parser = $this->mock('responseTypeIsModel');
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

        $parser = $this->mock('responseTypeIsModel');
        $this->assertFalse($parser->responseTypeIsModel($command));
    }

    /**
     * Calls the protected handleParsing() method on $parser
     *
     * Additional arguments to this function (other than $parser) will
     * be passed on as the arguments to the handleParsing() function.
     *
     * @param Desk\Relationship\ResponseParser $parser
     *
     * @return mixed
     */
    private function invokeHandleParsing(ResponseParser $parser)
    {
        $reflectionParser = new ReflectionObject($parser);
        $handleParsing = $reflectionParser->getMethod('handleParsing');
        $handleParsing->setAccessible(true);

        // get all arguments after the first
        $args = func_get_args();
        array_shift($args);

        return $handleParsing->invokeArgs($parser, $args);
    }
}
