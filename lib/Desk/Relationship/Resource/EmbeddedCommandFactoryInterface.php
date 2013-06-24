<?php

namespace Desk\Relationship\Resource;

use Guzzle\Service\Command\CommandInterface;

interface EmbeddedCommandFactoryInterface
{

    /**
     * Creates an EmbeddedCommand from an original command
     *
     * @param Guzzle\Service\Command\CommandInterface $originalCommand
     * @param array                                   $data
     *
     * @return Desk\Relationship\Resource\EmbeddedCommand
     */
    public function factory(CommandInterface $originalCommand, array $data);
}
