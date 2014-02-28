<?php

namespace Desk\Relationship\Resource;

use Desk\Relationship\Exception\UnknownResourceException;
use Guzzle\Service\Description\Parameter;
use Guzzle\Service\Resource\Model as GuzzleModel;

class Model extends GuzzleModel
{

    /**
     * Linked related resources
     *
     * @var array
     */
    private $links = array();

    /**
     * Embedded related resources
     *
     * @var array
     */
    private $embedded = array();


    /**
     * {@inheritdod}
     *
     * Overridden to provide special treatment to relationship data
     */
    public function __construct(array $data = array(), Parameter $structure = null)
    {
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
     * Retrieves a linked related resource from this model
     *
     * @param string $name The name of the related resource to retrieve
     *
     * @return \Guzzle\Service\Command\OperationCommand
     */
    public function getLink($name)
    {
        if (!$this->hasLink($name)) {
            throw new UnknownResourceException(
                "No related resource named '$name'"
            );
        }

        return $this->links[$name];
    }

    /**
     * Determines whether a linked related resource exists
     *
     * @param string $name The name of the related resource to look for
     *
     * @return boolean
     */
    public function hasLink($name)
    {
        return isset($this->links[$name]);
    }

    /**
     * Retrieves an embedded related resource from this model
     *
     * @param string $name The name of the related resource to retrieve
     *
     * @return \Guzzle\Service\Resource\Model
     */
    public function getEmbedded($name)
    {
        if (!$this->hasEmbedded($name)) {
            throw new UnknownResourceException(
                "No related resource named '$name'"
            );
        }

        return $this->embedded[$name];
    }

    /**
     * Determines whether a linked related resource exists
     *
     * @param string $name The name of the related resource to look for
     *
     * @return boolean
     */
    public function hasEmbedded($name)
    {
        return isset($this->embedded[$name]);
    }

    /**
     * Retrieves a related resource however possible
     *
     * The requested resource will always be returned directly. If the
     * resource was embedded into this model, that is used. Otherwise,
     * if it's only available as a link, then the link command will be
     * retrieved and executed to obtain the resource from the API.
     *
     * Behaves the same as getEmbedded(), but it can be used regardless
     * of whether or not the resource was embedded at the time of the
     * request where this model was created. If it did happen to be
     * embedded, then another request will be avoided; otherwise the
     * model will be retrieved from the API.
     *
     * @param string $name The name of the related resource to retrieve
     *
     * @return \Guzzle\Service\Resource\Model
     */
    public function getResource($name)
    {
        if ($this->hasEmbedded($name)) {
            return $this->getEmbedded($name);
        }

        return $this->getLink($name)->execute();
    }
}
