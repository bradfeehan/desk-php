<?php

namespace Desk\Relationship\Visitor\Response;

use Guzzle\Http\Message\Response;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;
use Desk\Relationship\Resource\ModelBuilder;
use Desk\Relationship\Resource\ModelBuilderInterface;
use Desk\Relationship\Visitor\ResponseVisitor;

/**
 * Processes embedded resource data into actual Model objects
 *
 * This response visitor parses the _embedded element in the response,
 * and creates Model objects that contain the embedded resource data.
 */
class EmbeddedVisitor extends ResponseVisitor
{

    /**
     * The key in responses where embedded resource data is stored
     *
     * @var string
     */
    const ELEMENT = '_embedded';


    /**
     * Builds models from embedded resources
     *
     * @var \Desk\Relationship\Resource\ModelBuilderInterface
     */
    private $builder;


    /**
     * Accepts a ModelBuilderInterface object, used to build models
     *
     * @param \Desk\Relationship\Resource\ModelBuilderInterface $builder
     */
    public function __construct(ModelBuilderInterface $builder = null)
    {
        $this->builder = $builder ?: new ModelBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function before(CommandInterface $command, array &$result)
    {
        $json = $command->getResponse()->json();

        // store embedded resources to use later
        if (array_key_exists(self::ELEMENT, $json)) {
            $this->set($command, 'embedded', $json[self::ELEMENT]);
        }

        // create new array of embedded resources which visit() adds to
        $result[self::ELEMENT] = array();
    }

    /**
     * {@inheritdoc}
     */
    public function visit(CommandInterface $command, Response $response, Parameter $param, &$value, $context = null)
    {
        // check if there's an embedded resource for the parameter's
        // "wire" name
        $resources = $this->get($command, 'embedded');
        if (!empty($resources[$param->getWireName()])) {
            // create a model representing the embedded resource data
            $embeddedModel = $this->builder->createEmbeddedModel(
                $command,
                $param,
                $resources[$param->getWireName()]
            );

            // store the created embedded model in the results array
            $value[self::ELEMENT][$param->getName()] = $embeddedModel;
        }
    }
}
