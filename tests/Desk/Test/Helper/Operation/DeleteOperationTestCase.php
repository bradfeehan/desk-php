<?php

namespace Desk\Test\Helper\Operation;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\OperationTestCase;

/**
 * Base class for any Delete* operation tests
 *
 * Subclasses must only implement the abstract assertSystem() method.
 * The testParameterValid() and testParameterInvalid() data providers
 * have been set up with parameter values that should be fine for all
 * Delete operations.
 *
 * To add further test cases for testParameterValid() or
 * testParameterInvalid() in a subclass, simply override
 * dataParameterValidAdditional() and dataParameterInvalidAdditional()
 * respectively.
 */
abstract class DeleteOperationTestCase extends OperationTestCase
{

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
        return array_merge(array('id' => 1), $overrides);
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array(
            array($this->getExampleParameters()),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array()),
            array(array('id' => true)),
            array(array('id' => false)),
            array(array('id' => null)),
            array(array('id' => 12.3)),
            array(array('id' => -12.3)),
            array(array('id' => new \stdClass())),
        );
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

        $result = $command->execute();
        $this->assertInternalType('null', $result);
    }
}
