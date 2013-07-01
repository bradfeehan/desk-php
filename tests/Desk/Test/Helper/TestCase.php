<?php

namespace Desk\Test\Helper;

use Desk\Client as DeskClient;
use Guzzle\Common\Exception\RuntimeException;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Tests\GuzzleTestCase;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionObject;

abstract class TestCase extends GuzzleTestCase
{

    /**
     * Cache for the value of canServiceDescriptionBeLoaded()
     *
     * @var boolean
     */
    private static $serviceDescriptionCanBeLoaded;


    public function setUp()
    {
        $this->clearInstances();
    }

    public function tearDown()
    {
        $this->clearInstances();
    }

    /**
     * Clears the singleton instance stored in the Factory
     */
    private function clearInstances()
    {
        DeskClient::setFactory();
    }

    /**
     * The base path of all test cases (usually PROJECT_BASE_DIR/tests)
     *
     * @var string
     */
    private static $testsBasePath;


    /**
     * Set up the base path of all test cases
     *
     * @param string $path The path to the test cases' root folder
     */
    public static function setTestsBasePath($path)
    {
        self::$testsBasePath = $path;
    }


    /**
     * Set a mock response from a mock file on the next client request
     *
     * Overridden to use some magic to determine the location of the
     * mock files. If the test case is defined in:
     *
     *   TESTS_BASE_PATH/Foo/Bar/Baz/QuuxTest.php
     *
     * then a $responseName of "grault" will be found in:
     *
     *   MOCK_BASE_PATH/Foo/Bar/Baz/QuuxTest/grault.txt
     *
     * where TESTS_BASE_PATH and MOCK_BASE_PATH are set up correctly
     * by self::setTestsBasePath() and self::setMockBasePath().
     *
     * @param Guzzle\Service\Client $client        Client object to modify
     * @param string|array          $responseNames Path to files within the Mock
     *                                             folder of the service
     *
     * @return Guzzle\Plugin\Mock\MockPlugin The created mock plugin
     */
    public function setMockResponse(Client $client, $responseNames)
    {
        $paths = array();

        foreach ((array) $responseNames as $responseName) {
            $paths[] = $this->getMockResponseDirectory() . "/$responseName.txt";
        }

        return parent::setMockResponse($client, $paths);
    }

    /**
     * Gets the path for this test case's mock responses
     *
     * @return string
     */
    private function getMockResponseDirectory()
    {
        $path = $this->getChildClassDirectory();

        // remove test base path, mock response base path is prepended
        $testsBasePath = preg_quote(self::$testsBasePath, '#');
        $path = preg_replace("#^{$testsBasePath}/?#", '', $path);

        return "$path/" . $this->getChildClassName();
    }

    /**
     * Gets the class name of the child class (without namespace)
     *
     * @return string
     */
    private function getChildClassName()
    {
        $class = explode('\\', get_called_class());
        return end($class);
    }

    /**
     * Gets the directory the child class test case is in
     *
     * So not __DIR__ (that would be the directory *this* file is in),
     * but the directory which contains the file containing the
     * definition of the concrete subclass of this class.
     *
     * @return string
     */
    private function getChildClassDirectory()
    {
        $reflectionClass = new ReflectionClass($this);
        return dirname($reflectionClass->getFileName());
    }

    /**
     * Gets the ReflectionProperty for a private property on any object
     *
     * @param mixed  $object       The object with the property to get
     * @param string $propertyName The name of the property to get
     *
     * @return ReflectionProperty
     */
    private function getProperty($object, $propertyName)
    {
        $className = get_class($object);
        $class = new ReflectionClass($className);

        // Private properties on parent classes can't be accessed by
        // getProperty(), so we have to ascend the class heirarchy
        // until we find the class it was actually defined on
        while ($class && !$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }

        if (!$class) {
            throw new InvalidArgumentException(
                "Property '$propertyName' not found on '$className' " .
                "or any of its parent classes"
            );
        }

        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        return $property;
    }

    /**
     * Gets the value of a private or protected property on any object
     *
     * @param mixed  $object       The object with the property to get
     * @param string $propertyName The name of the property to get
     *
     * @return mixed
     */
    public function getPrivateProperty($object, $propertyName)
    {
        $property = $this->getProperty($object, $propertyName);
        return $property->getValue($object);
    }

    /**
     * Gets the value of a private or protected property on any object
     *
     * @param mixed  $object       The object with the property to get
     * @param string $propertyName The name of the property to get
     * @param mixed  $value        The new value to set the property to
     *
     * @return mixed
     */
    public function setPrivateProperty($object, $propertyName, $value)
    {
        $property = $this->getProperty($object, $propertyName);
        return $property->setValue($object, $value);
    }

    /**
     * Determines whether the desk.json service description is valid
     *
     * @return boolean
     */
    protected function canServiceDescriptionBeLoaded()
    {
        if (self::$serviceDescriptionCanBeLoaded === null) {
            self::$serviceDescriptionCanBeLoaded = false;

            $filename = DeskClient::getDescriptionFilename();

            try {
                $description = ServiceDescription::factory($filename);
                self::$serviceDescriptionCanBeLoaded = true;
            } catch (RuntimeException $e) {
                // leave it as false
            }
        }

        return self::$serviceDescriptionCanBeLoaded;
    }
}
