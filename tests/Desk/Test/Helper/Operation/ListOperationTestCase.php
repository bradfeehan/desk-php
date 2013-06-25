<?php

namespace Desk\Test\Helper\Operation;

use Desk\Test\Helper\OperationTestCase;

/**
 * Base class for any List* operation tests
 *
 * Subclasses must only implement the abstract assertSystem() method.
 * The testParameterValid() and testParameterInvalid() data providers
 * have been set up with parameter values that should be fine for all
 * List operations.
 *
 * To add further test cases for testParameterValid() or
 * testParameterInvalid() in a subclass, simply override
 * dataParameterValidAdditional() and dataParameterInvalidAdditional()
 * respectively.
 */
abstract class ListOperationTestCase extends OperationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array_merge(
            array(
                array(array()),
                array(array('page' => 2), array('query' => '#^page=2$#')),
                array(array('per_page' => 3), array('query' => '#^per_page=3$#')),
                array(array('page' => 4, 'per_page' => 5), array('query' => '#^per_page=5&page=4$#')),
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
                array(array('page' => true)),
                array(array('page' => false)),
                array(array('page' => 0)),
                array(array('page' => -1)),
                array(array('page' => 1.4)),
                array(array('page' => -2.5)),
                array(array('page' => '8')),
                array(array('page' => new \stdClass())),
                array(array('per_page' => true)),
                array(array('per_page' => false)),
                array(array('per_page' => 0)),
                array(array('per_page' => -1)),
                array(array('per_page' => 3.4)),
                array(array('per_page' => -7.3)),
                array(array('per_page' => '5')),
                array(array('per_page' => new \stdClass())),
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
     * Gets the command ready for the system test
     *
     * @return Guzzle\Service\Command\AbstractCommand
     */
    protected function getSystemTestCommand()
    {
        $client = $this->client();
        $this->setMockResponse($client, 'system');

        return $client->getCommand($this->getOperationName());
    }

    /**
     * {@inheritdoc}
     */
    public function testSystem()
    {
        $command = $this->getSystemTestCommand();
        $this->assertInstanceOf(
            'Guzzle\\Service\\Command\\CommandInterface',
            $command,
            get_called_class() . "::getSystemTestCommand should " .
            "return a Guzzle Command object, got " .
            (is_object($command) ? get_class($command) : gettype($command))
        );

        $results = $command->execute();
        $this->assertInstanceOf('Desk\\Relationship\\Resource\\Model', $results);

        $models = $results->getEmbedded('entries');

        // $models should be an array of models of length total_entries
        $this->assertInternalType('array', $models);
        $this->assertSame(count($models), $results->get('total_entries'));

        foreach ($models as $model) {
            $this->assertInstanceOf('Desk\\Relationship\\Resource\\Model', $model);
        }

        // Perform child class assertions on the resulting models
        $this->assertSystem($models);
    }

    /**
     * Contains assertions to make about the results of the system test
     *
     * @param array $models Resulting models from system test
     */
    abstract protected function assertSystem(array $models);
}
