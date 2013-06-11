<?php

namespace Desk\Command;

use Guzzle\Service\Command\OperationCommand;

class NullResponseCommand extends OperationCommand
{

    /**
     * {@inheritdoc}
     *
     * Overridden to always set $this->result to NULL.
     */
    protected function process()
    {
        $this->result = null;
    }
}
