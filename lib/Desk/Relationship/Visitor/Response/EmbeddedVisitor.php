<?php

namespace Desk\Relationship\Visitor\Response;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;
use Desk\Relationship\Resource\ModelBuilder;
use Desk\Relationship\Resource\ModelBuilderInterface;

/**
 * Processes embedded resource data into actual Model objects
 *
 * This response visitor parses the _embedded element in the response,
 * and creates Model objects that contain the embedded resource data.
 */
class EmbeddedVisitor extends AbstractVisitor
{

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
    protected function getFieldName()
    {
        return 'embedded';
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceFromData(CommandInterface $command, Parameter $structure, array $data)
    {
        return $this->builder->createEmbeddedModel($command, $structure, $data);
    }
}
