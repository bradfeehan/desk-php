<?php

namespace Desk\Relationship;

interface ResourceBuilderInterface
{

    /**
     * Creates a Desk command representing a link to a resource
     *
     * @param array $link A node unter "_links" in a Desk response
     *
     * @return Desk\Relationship\Command
     */
    public function createCommandFromLink(array $link);

    /**
     * Creates a model representing an embedded resource
     *
     * @param array $data A node under "_embedded" in a Desk response
     *
     * @return Desk\Relationship\Model
     */
    public function createModelFromEmbedded(array $data);
}
