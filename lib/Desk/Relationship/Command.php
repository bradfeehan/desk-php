<?php

namespace Desk\Relationship;

use Guzzle\Service\Command\OperationCommand;

class Command extends OperationCommand
{

    /**
     * Sets the full URI for this command only
     *
     * This will override the URI set in the service description.
     *
     * @param string $uri
     *
     * @return Desk\Relationship\Command $this
     * @chainable
     */
    public function setUri($uri)
    {
        // Because $this->operation is a reference to the operation
        // in the service description, simply altering the URI will
        // change it for all future commands from the same client.
        // To avoid this, we have to clone it first.
        $operation = clone $this->getOperation();
        $operation->setUri($uri);
        $this->operation = $operation;

        // The URI overrides all parameters, so don't validate them
        $this->set(self::DISABLE_VALIDATION, true);

        return $this;
    }
}
