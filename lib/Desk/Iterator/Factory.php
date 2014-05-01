<?php

namespace Desk\Iterator;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Resource\AbstractResourceIteratorFactory;

class Factory extends AbstractResourceIteratorFactory
{

    /**
     * The class name of the iterator class created by this factory
     *
     * @var string
     */
    private $iteratorClassName = 'Desk\\Iterator\\ResourceIterator';


    /**
     * {@inheritdoc}
     */
    protected function getClassName(CommandInterface $command)
    {
        // If it's a ListWidgets command, we can iterate over it
        if (preg_match('/^List[A-Za-z]+/', $command->getName())) {
            return $this->iteratorClassName;
        }

        // Otherwise, we don't know how to iterate over that command
        return null;
    }
}
