<?php

namespace Desk\Test\Helper\Operation;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\OperationTestCase;

/**
 * Base class for any Show* operation tests
 *
 * Subclasses must only implement the abstract assertSystem() method.
 * The testParameterValid() and testParameterInvalid() data providers
 * have been set up with parameter values that should be fine for all
 * Show operations.
 *
 * To add further test cases for testParameterValid() or
 * testParameterInvalid() in a subclass, simply override
 * dataParameterValidAdditional() and dataParameterInvalidAdditional()
 * respectively.
 */
abstract class ShowOperationTestCase extends OperationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array_merge(
            array(
                array($this->getExampleParameters()),
            ),
            $this->dataParameterValidAdditional()
        );
    }

    /**
     * Adds to the default data provider for testParameterValid()
     *
     * Override this in a subclass to add additional valid parameter
     * test cases. The format should be the same as dataParameterValid:
     *
     * array(
     *   array($parameters1, $assertions1),
     *   array($parameters2, $assertions2),
     *   // ...
     * );
     *
     * @return array
     */
    public function dataParameterValidAdditional()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        // $tests contains a row for each test run -- each element in
        // the array is an argument to testParameterInvalid. Start with
        // basic invalid calls...
        $tests = array_merge(
            array(
                array(array()),
                array($this->getExampleParameters(array('embed' => 'foo'))),
                array($this->getExampleParameters(array('embed' => 'self'))),
            ),
            $this->dataParameterInvalidAdditional()
        );

        // add invalid calls, use non-integer values in integer params
        $nonIntegers = array(
            true, false, null, 12.3, -12.3, new \stdClass(),
        );

        foreach ($this->getIntegerIdProperties() as $name) {
            foreach ($nonIntegers as $value) {
                $tests[] = array(
                    $this->getExampleParameters(array($name => $value)),
                );
            }
        }

        return $tests;
    }

    /**
     * Adds to the default data provider for testParameterInvalid()
     *
     * Override this in a subclass to add additional valid parameter
     * test cases. The format should be the same as dataParameterValid:
     *
     * array(
     *   array($parameters1),
     *   array($parameters2),
     *   // ...
     * );
     *
     * @return array
     */
    public function dataParameterInvalidAdditional()
    {
        return array();
    }

    /**
     * Gets parameters for this operation
     *
     * This will return an associative array with parameter names for
     * keys mapping to values for the parameter. The parameters will
     * represent a valid call to this operation, unless overrides are
     * specified.
     *
     * Overrides are specified in the same way and will add to the
     * returned array (overriding any that would normally be returned).
     */
    protected function getExampleParameters(array $overrides = array())
    {
        $properties = array();

        foreach ($this->getIntegerIdProperties() as $property) {
            $properties[$property] = 1;
        }

        foreach ($overrides as $key => $value) {
            $properties[$key] = $value;
        }

        return $properties;
    }

    /**
     * Gets which properties should accept valid integer IDs
     *
     * @return array
     */
    protected function getIntegerIdProperties()
    {
        return array('id');
    }

    /**
     * {@inheritdoc}
     */
    public function testSystem()
    {
        $client = $this->client();
        $this->setMockResponse($client, 'system');

        $params = $this->getExampleParameters();
        $command = $client->getCommand($this->getOperationName(), $params);

        $model = $command->execute();

        // Perform child class asesrtions on the resulting model
        $this->assertInstanceOf('Desk\\Relationship\\Resource\\Model', $model);
        $this->assertSystem($model);
    }

    /**
     * Contains assertions to make about the results of the system test
     *
     * @param array $model Resulting model from system test
     */
    abstract protected function assertSystem(Model $model);

    /**
     * Tests links of models
     *
     * @dataProvider dataLinks
     * @depends testSystem
     *
     * @param string $linkName    The name of the link to test
     * @param string $commandName The expected name of the link command
     * @param array  $parameters  Expected command parameters to be set
     */
    public function testLinks($linkName, $commandName = false, array $parameters = null)
    {
        $client = $this->client();
        $this->setMockResponse($client, 'system');

        $params = $this->getExampleParameters();
        $command = $client->getCommand($this->getOperationName(), $params);

        $model = $command->execute();
        $link = $model->getLink($linkName);

        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $link);

        if ($commandName) {
            $this->assertSame($commandName, $link->getName());
        }

        if ($parameters) {
            foreach ($parameters as $parameter => $expected) {
                $this->assertSame($expected, $link->get($parameter));
            }
        }
    }

    public function dataLinks()
    {
        return array_merge(
            array(
                array('self', $this->getOperationName(), $this->getExampleParameters()),
            ),
            $this->dataLinksAdditional()
        );
    }

    /**
     * Adds to the default data provider for dataLinks()
     *
     * Override this in a subclass to add additional links to be tested
     * by testLinks(). The format should be the same as dataLinks():
     *
     * array(
     *   array($linkName1, $commandName1, $parameters1),
     *   array($linkName2, $commandName2, $parameters2),
     *   // ...
     * );
     *
     * @return array
     */
    public function dataLinksAdditional()
    {
        return array();
    }
}
