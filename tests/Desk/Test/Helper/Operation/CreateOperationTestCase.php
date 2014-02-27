<?php

namespace Desk\Test\Helper\Operation;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\OperationTestCase;

/**
 * Base class for any Create* operation tests
 *
 * Subclasses must only implement the abstract assertSystem() method,
 * as well as the dataParameterValid() and dataParameterInvalid()
 * abstract method from OperationTestCase.
 *
 * Links in the response are tested as well. By default, only the
 * "self" link is tested (as this is the only one that we can be sure
 * will exist for all Create operations). To add additional links to
 * test, implement the dataLinksAdditional() method.
 */
abstract class CreateOperationTestCase extends OperationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function testSystem()
    {
        $client = $this->client();
        $this->setMockResponse($client, 'system');

        $params = $this->getSystemParameters();
        $command = $client->getCommand($this->getOperationName(), $params);

        $model = $command->execute();

        // Perform child class asesrtions on the resulting model
        $this->assertInstanceOf('Desk\\Relationship\\Resource\\Model', $model);
        $this->assertSystem($model);
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
     *
     * @param array $overrides Parameters to override the defaults
     *
     * @return array
     */
    private function getSystemParameters(array $overrides = array())
    {
        $parameters = $this->getDefaultSystemParameters();

        foreach ($overrides as $key => $value) {
            $parameters[$key] = $value;
        }

        return $parameters;
    }

    /**
     * Gets the default example parameters for this operation
     *
     * This is used in getSystemParameters() as a starting point. Any
     * overrides provided to that function will override the return
     * value of this function.
     *
     * @return array
     */
    abstract protected function getDefaultSystemParameters();

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

        $params = $this->getSystemParameters();
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
                array('self', $this->getSelfOperationName()),
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

    /**
     * Gets the name of the operation for the "self" response link
     *
     * The default is to replace the "Create" at the start of the
     * result of $this->getOperationName() with "Show". So,
     * for example, "CreateFoo" becomes "ShowFoo".
     *
     * @return string
     */
    protected function getSelfOperationName()
    {
        return preg_replace('/^Create/', 'Show', $this->getOperationName());
    }
}
