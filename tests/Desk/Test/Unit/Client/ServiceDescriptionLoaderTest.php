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

        // All this, just to call a protected method passing args by reference!
        $loader = $this->mock();
        $reflectionLoader = new ReflectionObject($loader);
        $method = $reflectionLoader->getMethod('resolveExtension');
        $method->setAccessible(true);
        $method->invokeArgs($loader, array('fooOperation', &$op, &$operations));

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
        $reflectionLoader = new ReflectionObject($loader);
        $method = $reflectionLoader->getMethod('resolveExtension');
        $method->setAccessible(true);
        $method->invokeArgs($loader, array('fooOperation', &$op, &$operations));

        $this->assertTrue(isset($op['parameters']['fooParameter']['description']));
        $this->assertSame('fooDescription', $op['parameters']['fooParameter']['description']);

        $this->assertTrue(isset($op['parameters']['barParameter']['description']));
        $this->assertSame('barDescription', $op['parameters']['barParameter']['description']);
    }
}
