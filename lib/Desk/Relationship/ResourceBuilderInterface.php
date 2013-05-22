<?php

namespace Desk\Relationship;

interface ResourceBuilderInterface
{

    /**
     * Creates a Desk command representing a link to a resource
     *
     * @param string $linkName The name of the link in question
     * @param array  $data     A node unter "_links" in a Desk response
     *
     * @return Guzzle\Service\Command\OperationCommand
     */
    public function createCommandFromLink($linkName, array $data);

    /**
     * Creates a model representing an embedded resource
     *
     * @param string $linkName The name of the link in question
     * @param array  $data     A node under "_embedded" in a response
     *
     * @return Desk\Relationship\Model
     */
    public function createModelFromEmbedded($linkName, array $data);
}
