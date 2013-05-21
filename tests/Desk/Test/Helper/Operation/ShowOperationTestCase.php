<?php

namespace Desk\Test\Helper\Operation;

use Desk\Test\Helper\OperationTestCase;

/**
 * Base class for any Show* operation tests
 *
 * Subclasses must only implement the abstract testSystem() method
 * from OperationTestCase. The testParameterValid() and
 * testParameterInvalid() data providers have been set up with
 * parameter values that should be fine for all Show operations.
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
}
