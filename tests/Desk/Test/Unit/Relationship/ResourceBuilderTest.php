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
        $client = \Mockery::mock('Guzzle\\Service\\Client');
        $builder = new ResourceBuilder($client);

        $builderClient = $this->getPrivateProperty($builder, 'client');
        $this->assertSame($client, $builderClient);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createCommandFromLink
     */
    public function testCreateCommandFromLink()
    {
        $data = array(
            'class' => 'fooClass',
            'href' => '/path/to/foo',
        );

        $desc = array(
            'operation' => 'fooOperation',
            'pattern' => '$pattern',
        );

        $client = \Mockery::mock('Guzzle\\Service\\Client')
            ->shouldReceive('getCommand')
                ->with('fooOperation', array('the' => 'params'))
                ->andReturn('returnValue')
            ->getMock();

        $builder = $this->mock('createCommandFromLink', array($client))
            ->shouldReceive('validateLink')
                ->with($data)
            ->shouldReceive('validateLinkDescription')
                ->with($desc)
            ->shouldReceive('parseHref')
                ->with('/path/to/foo', '$pattern')
                ->andReturn(array('the' => 'params'))
            ->getMock();

        $command = $builder->createCommandFromLink('fooLink', $data, $desc);

        $this->assertSame('returnValue', $command);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     */
    public function testCreateModelFromEmbedded()
    {
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $client = \Mockery::mock('Guzzle\\Service\\Client');
        $client
            ->shouldReceive('getDescription->getModel')
                ->with('fooModel')
                ->andReturn($structure);

        $desc = array('model' => 'fooModel');
        $data = array('foo' => 'bar');

        $builder = $this->mock('createModelFromEmbedded', array($client))
            ->shouldReceive('validateEmbedDescription')
                ->with($desc)
            ->getMock();

        $model = $builder->createModelFromEmbedded('fooEmbed', $data, $desc);

        $this->assertInstanceOf('Desk\\Relationship\\Model', $model);
        $this->assertSame($structure, $model->getStructure());
        $this->assertSame($data, $model->toArray());

        $modelBuilder = $this->getPrivateProperty($model, 'builder');
        $this->assertSame($builder, $modelBuilder);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::createModelFromEmbedded
     */
    public function testCreateModelFromEmbeddedWithArray()
    {
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $client = \Mockery::mock('Guzzle\\Service\\Client');
        $client
            ->shouldReceive('getDescription->getModel')
                ->with('fooModel')
                ->andReturn($structure);

        $desc = array('model' => 'fooModel');
        $data = array(
            array('foo' => 'bar'),
            array('bar' => 'baz'),
        );

        $builder = $this->mock('createModelFromEmbedded', array($client))
            ->shouldReceive('validateEmbedDescription')
                ->with($desc)
            ->getMock();

        $models = $builder->createModelFromEmbedded('fooEmbed', $data, $desc);

        $this->assertInternalType('array', $models);

        $i = 0;
        foreach ($models as $model) {
            $this->assertInstanceOf('Desk\\Relationship\\Model', $model);
            $this->assertSame($structure, $model->getStructure());
            $this->assertSame($data[$i++], $model->toArray());

            $modelBuilder = $this->getPrivateProperty($model, 'builder');
            $this->assertSame($builder, $modelBuilder);
        }
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLink
     */
    public function testValidateLink()
    {
        $builder = $this->mock('validateLink');
        $builder->validateLink(
            array(
                'href' => '/path/to/foo',
                'class' => 'fooClass',
            )
        );

        // Necessary to prevent PHPUnit complaining about no assertions
        // The real "assertion" is that no exceptions are thrown
        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLink
     * @expectedException Desk\Relationship\Exception\InvalidLinkFormatException
     * @expectedExceptionMessage Invalid resource link format: missing expected 'href' element
     */
    public function testValidateLinkMissingHref()
    {
        $builder = $this->mock('validateLink');
        $builder->validateLink(
            array(
                'not_href' => '/path/to/foo',
                'class' => 'fooClass',
            )
        );
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLinkDescription
     */
    public function testValidateLinkDescription()
    {
        $builder = $this->mock('validateLinkDescription');
        $builder->validateLinkDescription(
            array(
                'operation' => 'fooOperation',
                'pattern' => '#/foo/(?P<id>\\d+)$#',
            )
        );

        // Necessary to prevent PHPUnit complaining about no assertions
        // The real "assertion" is that no exceptions are thrown
        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLinkDescription
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Missing operation for link description
     */
    public function testValidateLinkDescriptionMissingOperation()
    {
        $builder = $this->mock('validateLinkDescription');
        $builder->validateLinkDescription(
            array(
                'not_operation' => 'fooOperation',
                'pattern' => '#/foo/(?P<id>\\d+)$#',
            )
        );
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateLinkDescription
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Missing pattern for link description
     */
    public function testValidateLinkDescriptionMissingPattern()
    {
        $builder = $this->mock('validateLinkDescription');
        $builder->validateLinkDescription(
            array(
                'operation' => 'fooOperation',
                'not_pattern' => '#/foo/(?P<id>\\d+)$#',
            )
        );
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateEmbedDescription
     */
    public function testValidateEmbedDescription()
    {
        $builder = $this->mock('validateEmbedDescription');
        $builder->validateEmbedDescription(
            array(
                'model' => 'fooModel',
            )
        );

        // Necessary to prevent PHPUnit complaining about no assertions
        // The real "assertion" is that no exceptions are thrown
        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Relationship\ResourceBuilder::validateEmbedDescription
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Missing model for embed description
     */
    public function testValidateEmbedDescriptionMissingModel()
    {
        $builder = $this->mock('validateEmbedDescription');
        $builder->validateEmbedDescription(
            array(
                'not_model' => 'fooModel',
            )
        );
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
}
