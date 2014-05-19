<?php

namespace Desk\Test\Helper\Operation;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;

/**
 * Base class for any Update* operation tests
 *
 * Subclasses must only implement the abstract assertSystem() method,
 * as well as the dataParameterValid() and dataParameterInvalid()
 * abstract method from OperationTestCase.
 *
 * Links in the response are tested as well. By default, only the
 * "self" link is tested (as this is the only one that we can be sure
 * will exist for all Update operations). To add additional links to
 * test, implement the dataLinksAdditional() method.
 */
abstract class UpdateOperationTestCase extends CreateOperationTestCase
{

    /**
     * Gets the name of the operation for the "self" response link
     *
     * The default is to replace the "Update" at the start of the
     * result of $this->getOperationName() with "Show". So,
     * for example, "UpdateFoo" becomes "ShowFoo".
     *
     * @return string
     */
    protected function getSelfOperationName()
    {
        return preg_replace('/^Update/', 'Show', $this->getOperationName());
    }
}
