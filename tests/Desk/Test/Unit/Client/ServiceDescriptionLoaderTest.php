<?php

namespace Desk\Test\Unit\Client;

use Desk\Client\ServiceDescriptionLoader;
use Desk\Test\Helper\UnitTestCase;
use ReflectionObject;

class ServiceDescriptionLoaderTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Client\\ServiceDescriptionLoader';
    }

    /**
     * @covers Desk\Client\ServiceDescriptionLoader::build
     */
    public function testBuild()
    {
        $fooProp = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');
        $barProp = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getName')
                ->andReturn('barProperty')
            ->getMock();

        $model = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getProperties')
                ->andReturn(array('fooProperty' => $fooProp))
            ->shouldReceive('getProperty')
                ->with('barProperty')
                ->andReturn(null)
            ->shouldReceive('addProperty')
                ->with($barProp)
                ->once()
            ->getMock();

        $model->extends = 'myParentModel';

        $parent = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getProperties')
                ->andReturn(array('barProperty' => $barProp))
            ->getMock();

        $descriptionClass = 'Guzzle\\Service\\Description\\ServiceDescription';
        $description = \Mockery::mock($descriptionClass)
            ->shouldReceive('getModels')
                ->andReturn(array($model))
            ->shouldReceive('getModel')
                ->with('myParentModel')
                ->andReturn($parent)
            ->getMock();

        $loader = $this->mock()
            ->shouldReceive('parentBuild')
                ->with(array(), array())
                ->andReturn($description)
            ->getMock();

        $result = $this->call($loader, 'build', array(array(), array()));

        $this->assertSame($description, $result);
    }

    /**
     * @covers Desk\Client\ServiceDescriptionLoader::build
     */
    public function testBuildWithModelWithoutProperties()
    {
        $model = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $descriptionClass = 'Guzzle\\Service\\Description\\ServiceDescription';
        $description = \Mockery::mock($descriptionClass)
            ->shouldReceive('getModels')
                ->andReturn(array($model))
            ->getMock();

        $loader = $this->mock()
            ->shouldReceive('parentBuild')
                ->with(array(), array())
                ->andReturn($description)
            ->getMock();

        $result = $this->call($loader, 'build', array(array(), array()));

        $this->assertSame($description, $result);
    }

    /**
     * @covers Desk\Client\ServiceDescriptionLoader::build
     */
    public function testBuildWithModelWithoutParentProperties()
    {
        $fooProp = \Mockery::mock('Guzzle\\Service\\Description\\Parameter');

        $model = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getProperties')
                ->andReturn(array('fooProperty' => $fooProp))
            ->getMock();

        $model->extends = 'myParentModel';

        $parent = \Mockery::mock('Guzzle\\Service\\Description\\Parameter')
            ->shouldReceive('getProperties')
                ->andReturn(array())
            ->getMock();

        $descriptionClass = 'Guzzle\\Service\\Description\\ServiceDescription';
        $description = \Mockery::mock($descriptionClass)
            ->shouldReceive('getModels')
                ->andReturn(array($model))
            ->shouldReceive('getModel')
                ->with('myParentModel')
                ->andReturn($parent)
            ->getMock();

        $loader = $this->mock()
            ->shouldReceive('parentBuild')
                ->with(array(), array())
                ->andReturn($description)
            ->getMock();

        $result = $this->call($loader, 'build', array(array(), array()));

        $this->assertSame($description, $result);
    }

    /**
     * @covers Desk\Client\ServiceDescriptionLoader::parentBuild
     */
    public function testParentBuild()
    {
        $result = $this->mock('parentBuild')->parentBuild(array(), array());
        $descriptionClass = 'Guzzle\\Service\\Description\\ServiceDescription';
        $this->assertInstanceOf($descriptionClass, $result);
    }

    /**
     * @covers Desk\Client\ServiceDescriptionLoader::resolveExtension
     */
    public function testResolveExtension()
    {
        $op = array(
            'extends' => 'barOperation',
            'parameters' => array(
                'fooParameter' => array(
                    'description' => 'fooDescription',
                ),
            ),
            'data' => array(
                'links' => array(
                    'fooLink' => array(
                        'foo' => 'bar',
                    ),
                ),
            ),
        );

        $operations = array(
            'fooOperation' => $op,
            'barOperation' => array(
                'parameters' => array(
                    'barParameter' => array(
                        'description' => 'barDescription',
                    ),
                ),
                'data' => array(
                    'links' => array(
                        'barLink' => array(
                            'bar' => 'baz',
                        ),
                    ),
                ),
            ),
        );

        $loader = $this->mock();
        $this->call($loader, 'resolveExtension', array('fooOperation', &$op, &$operations));

        // Should have its original fooLink
        $this->assertTrue(isset($op['data']['links']['fooLink']));
        $this->assertSame('bar', $op['data']['links']['fooLink']['foo']);

        // Should have barOperation's barLink too since it was extended
        $this->assertTrue(isset($op['data']['links']['barLink']));
        $this->assertSame('baz', $op['data']['links']['barLink']['bar']);

        // Should have its parameter
        $this->assertTrue(isset($op['parameters']['fooParameter']['description']));
        $this->assertSame('fooDescription', $op['parameters']['fooParameter']['description']);

        // Should have barOperation's parameter too
        $this->assertTrue(isset($op['parameters']['barParameter']['description']));
        $this->assertSame('barDescription', $op['parameters']['barParameter']['description']);
    }

    /**
     * @covers Desk\Client\ServiceDescriptionLoader::resolveExtension
     */
    public function testResolveExtensionWithNoData()
    {
        $op = array(
            'extends' => 'barOperation',
            'parameters' => array(
                'fooParameter' => array(
                    'description' => 'fooDescription',
                ),
            ),
        );

        $operations = array(
            'fooOperation' => $op,
            'barOperation' => array(
                'parameters' => array(
                    'barParameter' => array(
                        'description' => 'barDescription',
                    ),
                ),
            ),
        );

        $loader = $this->mock();
        $this->call($loader, 'resolveExtension', array('fooOperation', &$op, &$operations));

        $this->assertTrue(isset($op['parameters']['fooParameter']['description']));
        $this->assertSame('fooDescription', $op['parameters']['fooParameter']['description']);

        $this->assertTrue(isset($op['parameters']['barParameter']['description']));
        $this->assertSame('barDescription', $op['parameters']['barParameter']['description']);
    }

    /**
     * Calls a private method on any object
     *
     * @param mixed  $object     The object to call the method on
     * @param string $methodName The name of the method to call
     * @param array  $args       Arguments to pass to the method
     *
     * @return mixed
     */
    private function call($object, $methodName, $args = array())
    {
        $reflectionLoader = new ReflectionObject($object);
        $method = $reflectionLoader->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }
}
