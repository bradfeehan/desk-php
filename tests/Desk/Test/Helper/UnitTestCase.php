<?php

namespace Desk\Test\Helper;

use Desk\Test\Helper\TestCase;
use ReflectionClass;
use ReflectionMethod;

abstract class UnitTestCase extends TestCase
{

    /**
     * Gets the class name that will be mocked by self::mock()
     *
     * @return string
     */
    abstract protected function getMockedClass();

    /**
     * Creates a mocked instance of self::getMockedClass()
     *
     * All methods will be mocked, so they can be stubbed using
     * shouldReceive(), etc. Any method names passed in to $methods
     * will be passed through to the underlying implementation instead
     * of being mocked.
     *
     * @param array $methods         Methods to NOT mock (passthru)
     * @param array $constructorArgs Optional constructor args
     *
     * @return mixed
     */
    protected function mock($methods = array(), array $constructorArgs = array())
    {
        $mock = \Mockery::mock($this->getMockedClass(), $constructorArgs);

        foreach ((array) $methods as $method) {
            $mock->shouldReceive($method)->passthru();
        }

        return $mock;
    }
}
