<?php

namespace Desk\Test\Unit\Relationship\Resource;

use Desk\Test\Helper\UnitTestCase;
use Guzzle\Http\QueryString;

class CommandBuilderTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Resource\\CommandBuilder';
    }

    /**
     * @covers Desk\Relationship\Resource\CommandBuilder::createLinkCommand
     */
    public function testCreateLinkCommand()
    {
        $data = array(
            'class' => 'fooClass',
            'href' => '/path/to/foo',
        );

        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('operation')
                ->andReturn('$operation')
            ->shouldReceive('getData')
                ->with('pattern')
                ->andReturn('$pattern')
            ->getMock();

        $command = \Mockery::mock('Guzzle\\Service\\Command\\CommandInterface');
        $command
            ->shouldReceive('getClient->getCommand')
                ->with('$operation', array('the' => 'params'))
                ->andReturn('returnValue');

        $builder = $this->mock('createLinkCommand')
            ->shouldReceive('validateLink')
                ->with($data)
            ->shouldReceive('validateLinkStructure')
                ->with($structure)
            ->shouldReceive('parseHref')
                ->with('/path/to/foo', '$pattern')
                ->andReturn(array('the' => 'params'))
            ->getMock();

        $result = $builder->createLinkCommand($command, $structure, $data);

        $this->assertSame('returnValue', $result);
    }

    /**
     * @covers Desk\Relationship\Resource\CommandBuilder::validateLink
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
     * @covers Desk\Relationship\Resource\CommandBuilder::validateLink
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
     * @covers Desk\Relationship\Resource\CommandBuilder::validateLinkStructure
     */
    public function testValidateLinkStructure()
    {
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('operation')
                ->andReturn('$operation')
            ->shouldReceive('getData')
                ->with('pattern')
                ->andReturn('$pattern')
            ->getMock();

        $builder = $this->mock('validateLinkStructure');
        $builder->validateLinkStructure($structure);

        // Necessary to prevent PHPUnit complaining about no assertions
        // The real "assertion" is that no exceptions are thrown
        $this->assertTrue(true);
    }

    /**
     * @covers Desk\Relationship\Resource\CommandBuilder::validateLinkStructure
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Parameter with 'links' location requires 'operation'
     */
    public function testValidateLinkStructureMissingOperation()
    {
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('operation')
                ->andReturn(false)
            ->shouldReceive('getData')
                ->with('pattern')
                ->andReturn('$pattern')
            ->getMock();

        $builder = $this->mock('validateLinkStructure');
        $builder->validateLinkStructure($structure);
    }

    /**
     * @covers Desk\Relationship\Resource\CommandBuilder::validateLinkStructure
     * @expectedException Desk\Exception\UnexpectedValueException
     * @expectedExceptionMessage Parameter with 'links' location requires 'pattern'
     */
    public function testValidateLinkStructureMissingPattern()
    {
        $structure = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getData')
                ->with('operation')
                ->andReturn('$operation')
            ->shouldReceive('getData')
                ->with('pattern')
                ->andReturn(false)
            ->getMock();

        $builder = $this->mock('validateLinkStructure');
        $builder->validateLinkStructure($structure);
    }

    /**
     * @covers Desk\Relationship\Resource\CommandBuilder::parseHref
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
     * @covers Desk\Relationship\Resource\CommandBuilder::parseHref
     */
    public function testParseHrefWithSpecialQueryParameter()
    {
        $href = '/foo/bar/baz?qux=bongo&thud=grunt&blarg=wham,wibble';
        $pattern = '#^/foo/(?P<test>[a-z]+)/baz\\?(?P<_query>.*)$#';

        $builder = $this->mock('parseHref')
            ->shouldReceive('parseQueryString')
                ->with('qux=bongo&thud=grunt&blarg=wham,wibble')
                ->andReturn(new QueryString(array('foo' => 'bar')))
            ->getMock();
        $parameters = $builder->parseHref($href, $pattern);

        $this->assertInternalType('array', $parameters);

        $expected = array(
            'test' => 'bar',
            'foo' => 'bar',
        );

        $this->assertSame($expected, $parameters);
    }

    /**
     * @covers Desk\Relationship\Resource\CommandBuilder::parseHref
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
     * @covers Desk\Relationship\Resource\CommandBuilder::parseHref
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
     * @covers Desk\Relationship\Resource\CommandBuilder::parseQueryString
     */
    public function testParseQueryString()
    {
        $query = '?foo&bar=baz&qux=whimmy%20wham,wham%2C+wazzle%21';
        $expected = array(
            'foo' => '',
            'bar' => 'baz',
            'qux' => array(
                'whimmy wham',
                'wham, wazzle!',
            ),
        );

        $builder = $this->mock('parseQueryString');
        $result = $builder->parseQueryString($query);

        $this->assertSame($expected, $result->toArray());
    }
}
