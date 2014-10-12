<?php

namespace Desk\Relationship\Resource;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;

interface ModelBuilderInterface
{

    /**
     * Creates a Guzzle model representing an embedded resource
     *
     * Requires the original command (which contains the resource); the
     * model parameter (from the service description); and the model
     * data (from the command's response).
     *
     * @param \Guzzle\Service\Command\CommandInterface $command
     * @param \Guzzle\Service\Description\Parameter    $structure
     * @param array                                    $data
     *
     * @return \Desk\Relationship\Resource\Model|array
     */
    public function createEmbeddedModel(CommandInterface $command, Parameter $structure, array $data);
}
