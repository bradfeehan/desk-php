<?php

namespace Desk\Relationship;

use Desk\Exception\InvalidArgumentException;
use Desk\Relationship\ResourceBuilderInterface;
use Guzzle\Service\Description\Parameter;
use Guzzle\Service\Resource\Model as GuzzleModel;

/**
 * A relationship-aware model class
 */
class Model extends GuzzleModel
{

    /**
     * Resource builder which is used to create relationship resources
     *
     * @var Desk\Relationship\ResourceBuilderInterface
     */
    private $builder;

    /**
     * Related models that have been linked to from the API response
     *
     * @var array
     */
    private $links = array();

    /**
     * Related models that have been embedded in API response
     *
     * @var array
     */
    private $embedded = array();


    /**
     * Overridden to require a resource builder to create relationships
     *
     * @param Desk\Relationship\ResourceBuilderInterface $builder
     * @param array                                      $data
     * @param Guzzle\Service\Description\Parameter       $structure
     */
    public function __construct(ResourceBuilderInterface $builder, array $data = array(), Parameter $structure = null)
    {
        $this->builder = $builder;

        if (isset($data['_links'])) {
            $this->links = $data['_links'];
            unset($data['_links']);
        }

        if (isset($data['_embedded'])) {
            $this->embedded = $data['_embedded'];
            unset($data['_embedded']);
        }

        parent::__construct($data, $structure);
    }

    /**
     * Gets a command representing one of this model's linked resources
     *
     * @param string $linkName The name of the link (e.g. "self")
     *
     * @return Guzzle\Service\Command\OperationCommand
     */
    public function getLink($linkName)
    {
        if (empty($this->links[$linkName])) {
            throw new InvalidArgumentException(
                "Unknown link '$linkName'"
            );
        }

        $data = $this->links[$linkName];
        return $this->builder->createCommandFromLink($linkName, $data);
    }

    /**
     * Gets a model representing one of this model's embedded resources
     *
     * @param string $linkName The name of the embedded resource
     *
     * @return Desk\Relationship\Model
     */
    public function getEmbedded($linkName)
    {
        if (empty($this->embedded[$linkName])) {
            throw new InvalidArgumentException(
                "Unknown embedded resource '$linkName'"
            );
        }

        $data = $this->embedded[$linkName];
        return $this->builder->createModelFromEmbedded($linkName, $data);
    }
}
