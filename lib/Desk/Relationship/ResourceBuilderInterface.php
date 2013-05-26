<?php

namespace Desk\Relationship;

interface ResourceBuilderInterface
{

    /**
     * Creates a Desk command representing a link to a resource
     *
     * @param string $name        The name of the link in question
     * @param array  $data        A node unter "_links" in a Desk response
     * @param array  $description Link description from model
     *
     * @return Guzzle\Service\Command\OperationCommand
     */
    public function createCommandFromLink($name, array $data, array $description);

    /**
     * Creates a model representing an embedded resource
     *
     * @param string $name        The name of the link in question
     * @param array  $data        A node under "_embedded" in a response
     * @param array  $description Link description from model
     *
     * @return Desk\Relationship\Model
     */
    public function createModelFromEmbedded($name, array $data, array $description);
}
