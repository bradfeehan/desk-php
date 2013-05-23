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
        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $builder = new ResourceBuilder($command);

        $actualCommand = $this->getPrivateProperty($builder, 'command');
        $this->assertSame($command, $actualCommand);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createCommandFromLink
     */
    public function testCreateCommandFromLink()
    {
        $description = array(
            'operation' => 'fooOperation',
            'pattern' => '/thePattern/',
        );

        $data = array(
            'href' => '/path/to/resource',
            'class' => 'myClass',
        );

        $parameters = array('foo' => 'bar');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $command
            ->shouldReceive('getClient->getCommand')
                ->with('fooOperation', $parameters)
                ->andReturn('result');

        $builder = $this->mock('createCommandFromLink', array($command))
            ->shouldReceive('validateLink')
                ->with($data)
            ->shouldReceive('getLinkDescription')
                ->with('myLink')
                ->andReturn($description)
            ->shouldReceive('parseHref')
                ->with('/path/to/resource', '/thePattern/')
                ->andReturn($parameters)
            ->getMock();

        $result = $builder->createCommandFromLink('myLink', $data);
        $this->assertSame('result', $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createCommandFromLink
     */
    public function testCreateCommandFromLinkWithSelfOperation()
    {
        $description = array(
            'operation' => '$self',
            'pattern' => '/thePattern/',
        );

        $data = array(
            'href' => '/path/to/resource',
            'class' => 'myClass',
        );

        $parameters = array('foo' => 'bar');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $command
            ->shouldReceive('getName')
                ->andReturn('selfOperationName')
            ->shouldReceive('getClient->getCommand')
                ->with('selfOperationName', $parameters)
                ->andReturn('result');

        $builder = $this->mock('createCommandFromLink', array($command))
            ->shouldReceive('validateLink')
                ->with($data)
            ->shouldReceive('getLinkDescription')
                ->with('myLink')
                ->andReturn($description)
            ->shouldReceive('parseHref')
                ->with('/path/to/resource', '/thePattern/')
                ->andReturn($parameters)
            ->getMock();

        $result = $builder->createCommandFromLink('myLink', $data);
        $this->assertSame('result', $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     */
    public function testCreateModelFromEmbedded()
    {
        $data = array('foo' => 'bar');

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $command
            ->shouldReceive('getClient->getDescription->getModel')
                ->with('myModel')
                ->andReturn($structure);

        $builder = $this->mock('createModelFromEmbedded', array($command))
            ->shouldReceive('getEmbedDescription')
                ->with('myLink')
                ->andReturn(array('model' => 'myModel'))
            ->getMock();

        $model = $builder->createModelFromEmbedded('myLink', $data);

        $this->assertInstanceOf('Desk\\Relationship\\Model', $model);

        $this->assertSame($structure, $model->getStructure());
        $this->assertSame('bar', $model->get('foo'));

        $modelBuilder = $this->getPrivateProperty($model, 'builder');
        $this->assertSame($builder, $modelBuilder);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     */
    public function testCreateModelFromEmbeddedWithArray()
    {
        $data = array(
            array('foo' => 'bar'),
            array('bar' => 'baz'),
            array('baz' => 'qux'),
        );

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $command
            ->shouldReceive('getClient->getDescription->getModel')
                ->with('myModel')
                ->andReturn($structure);

        $builder = $this->mock('createModelFromEmbedded', array($command))
            ->shouldReceive('getEmbedDescription')
                ->with('myLink')
                ->andReturn(array('model' => 'myModel'))
            ->getMock();

        $models = $builder->createModelFromEmbedded('myLink', $data);

        $this->assertInternalType('array', $models);

        foreach ($models as $model) {
            $this->assertSame($structure, $model->getStructure());
            $modelBuilder = $this->getPrivateProperty($model, 'builder');
            $this->assertSame($builder, $modelBuilder);
        }

        $this->assertSame('bar', $models[0]->get('foo'));
        $this->assertSame('baz', $models[1]->get('bar'));
        $this->assertSame('qux', $models[2]->get('baz'));
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Unknown embedded resource model 'myModel'
     */
    public function testCreateModelFromEmbeddedWithUnknownModel()
    {
        $data = array('foo' => 'bar');

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $command
            ->shouldReceive('getClient->getDescription->getModel')
                ->with('myModel')
                ->andReturn(null);

        $builder = $this->mock('createModelFromEmbedded', array($command))
            ->shouldReceive('getEmbedDescription')
                ->with('myLink')
                ->andReturn(array('model' => 'myModel'))
            ->getMock();

        $builder->createModelFromEmbedded('myLink', $data);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getLinkDescription
     */
    public function testGetLinkDescription()
    {
        $builder = $this->mock('getLinkDescription')
            ->shouldReceive('getDescription')
                ->with('links', 'myLinkName')
                ->andReturn('returnValue')
            ->getMock();

        $result = $builder->getLinkDescription('myLinkName');

        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getEmbedDescription
     */
    public function testGetEmbedDescription()
    {
        $builder = $this->mock('getEmbedDescription')
            ->shouldReceive('getDescription')
                ->with('embeds', 'myLinkName')
                ->andReturn('returnValue')
            ->getMock();

        $result = $builder->getEmbedDescription('myLinkName');

        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getDescription
     */
    public function testGetDescription()
    {
        $links = array(
            'notMyLink' => '$notMyLinkDescription',
            'myLink' => '$myLinkDescription',
        );

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand');
        $command
            ->shouldReceive('getOperation->getData')
                ->with('myType')
                ->andReturn($links);

        $builder = $this->mock('getDescription', array($command));
        $result = $builder->getDescription('myType', 'myLink');

        $this->assertSame('$myLinkDescription', $result);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::getDescription
     * @expectedException Desk\Exception\InvalidArgumentException
     * @expectedExceptionMessage Operation 'myOperation' missing 'nonExistantLink' link description
     */
    public function testGetDescriptionUnknownLinkName()
    {
        $links = array(
            'fooLink' => '$fooLinkDescription',
            'barLink' => '$barLinkDescription',
        );

        $operation = \Mockery::mock()
            ->shouldReceive('getData')
                ->with('myType')
                ->andReturn($links)
            ->shouldReceive('getName')
                ->andReturn('myOperation')
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\AbstractCommand')
            ->shouldReceive('getOperation')
                ->andReturn($operation)
            ->getMock();

        $builder = $this->mock('getDescription', array($command));
        $builder->getDescription('myType', 'nonExistantLink');
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::parseHref
     */
    public function testParseHref()
    {
        $href = '/foo/bar/baz';
        $pattern = '#^/(?P<one>[a-z]+)/(?P<two>[a-z]+)/(?P<three>[a-z]+)$#';

        $builder = $this->mock('parseHref');
        $parameters = $builder->parseHref($href, $pattern);

        $this->assertInternalType('array', $parameters);

        $this->assertSame('foo', $parameters['one']);
        $this->assertSame('bar', $parameters['two']);
        $this->assertSame('baz', $parameters['three']);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::parseHref
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Couldn't parse parameters from link href
     */
    public function testParseHrefNoMatches()
    {
        $href = '/foo/bar/baz';
        $pattern = '#grault/(?P<one>[a-z]+)#';

        $builder = $this->mock('parseHref');
        $builder->parseHref($href, $pattern);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::parseHref
     */
    public function testParseHrefWithIntegerParameters()
    {
        $href = '/foo/bar/123';
        $pattern = '#^/foo/bar/(?P<id>[0-9]+)$#';

        $builder = $this->mock('parseHref');
        $parameters = $builder->parseHref($href, $pattern);

        $this->assertInternalType('array', $parameters);
        $this->assertSame(123, $parameters['id']); // integer type
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLink
     */
    public function testValidateLink()
    {
        $this->mock('validateLink')->validateLink(
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
        $builder = $this->mock('validateLink');
        $builder->validateLink(array('class' => 'missing href'));
    }
}
