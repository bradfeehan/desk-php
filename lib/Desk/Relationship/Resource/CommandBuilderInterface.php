<?php

namespace Desk\Relationship\Resource;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;

interface CommandBuilderInterface
{

    /**
     * Creates a Guzzle command representing a link to a resource
     *
     * Requires the original command (which contains the link); the
     * link parameter (from the service description); and the link data
     * (from the command's response).
     *
     * @param Guzzle\Service\Command\CommandInterface $command
     * @param Guzzle\Service\Description\Parameter    $structure
     * @param array                                   $data
     *
     * @return Guzzle\Service\Command\OperationCommand
     */
    public function createLinkCommand(CommandInterface $command, Parameter $structure, array $data);
}
