<?php

namespace Desk\Test\Helper\Operation;

use Desk\Relationship\Model;
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
                array(array('id' => 9)),
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
        return array_merge(
            array(
                array(array()),
                array(array('id' => 4, 'embed' => 'foo')),
                array(array('id' => 4, 'embed' => 'self')),
                array(array('id' => true)),
                array(array('id' => false)),
                array(array('id' => null)),
                array(array('id' => 0)),
                array(array('id' => -12)),
                array(array('id' => 12.3)),
                array(array('id' => -12.3)),
                array(array('id' => '3')),
                array(array('id' => new \stdClass())),
            ),
            $this->dataParameterInvalidAdditional()
        );
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
     * {@inheritdoc}
     */
    public function testSystem()
    {
        $client = $this->client();
        $this->setMockResponse($client, 'system');

        $params = array('id' => 1);
        $command = $client->getCommand($this->getOperationName(), $params);

        $model = $command->execute();

        // Perform child class asesrtions on the resulting model
        $this->assertInstanceOf('Desk\\Relationship\\Model', $model);
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

        $params = array('id' => 1);
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
                array('self', $this->getOperationName()),
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
