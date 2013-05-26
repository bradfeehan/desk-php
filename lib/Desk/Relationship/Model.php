<?php

namespace Desk\Relationship;

use Desk\Exception\InvalidArgumentException;
use Desk\Exception\UnexpectedValueException;
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
     * @param string $name The name of the link (e.g. "self")
     *
     * @return Guzzle\Service\Command\OperationCommand
     */
    public function getLink($name)
    {
        $desc = $this->getLinkDescription($name);

        if (empty($this->links[$name])) {
            throw new UnexpectedValueException(
                "Link '$name' not found on this model"
            );
        }

        $data = $this->links[$name];

        return $this->builder->createCommandFromLink($name, $data, $desc);
    }


    /**
     * Gets a model representing one of this model's embedded resources
     *
     * @param string $name The name of the embedded resource
     *
     * @return Desk\Relationship\Model
     */
    public function getEmbedded($name)
    {
        $desc = $this->getLinkDescription($name);

        if (empty($this->embedded[$name])) {
            throw new UnexpectedValueException(
                "Unknown embedded resource '$name'"
            );
        }

        $data = $this->embedded[$name];

        return $this->builder->createModelFromEmbedded($name, $data, $desc);
    }

    /**
     * Gets the description of a link from the model description
     *
     * @param string $name The name of the link (e.g. "self")
     *
     * @return array
     */
    public function getLinkDescription($name)
    {
        $links = $this->getStructure()->getData('links');

        if (empty($links[$name])) {
            throw new InvalidArgumentException(
                "Missing link description for link '$name'"
            );
        }

        return $links[$name];
    }
}
