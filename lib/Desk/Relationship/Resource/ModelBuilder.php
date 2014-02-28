<?php

namespace Desk\Relationship\Resource;

use Desk\Relationship\Resource\EmbeddedCommandFactory;
use Desk\Relationship\Resource\EmbeddedCommandFactoryInterface;
use Desk\Relationship\Resource\Model;
use Desk\Relationship\Resource\ModelBuilderInterface;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Command\LocationVisitor\VisitorFlyweight;
use Guzzle\Service\Description\Parameter;

class ModelBuilder implements ModelBuilderInterface
{

    /**
     * The VisitorFlyweight which contains LocationVisitors
     *
     * @var \Guzzle\Service\Command\LocationVisitor\VisitorFlyweight
     */
    protected $visitors;

    /**
     * The EmbeddedCommandFactory which builds EmbeddedCommands
     *
     * @var \Desk\Relationship\Resource\EmbeddedCommandFactoryInterface
     */
    protected $factory;


    /**
     * Accepts a VisitorFlyweight for LocationVisitors
     *
     * @param \Desk\Relationship\Resource\EmbeddedCommandFactoryInterface $factory
     * @param \Guzzle\Service\Command\LocationVisitor\VisitorFlyweight    $visitors
     */
    public function __construct(EmbeddedCommandFactoryInterface $factory = null, VisitorFlyweight $visitors = null)
    {
        $this->factory = $factory ?: new EmbeddedCommandFactory();
        $this->visitors = $visitors ?: VisitorFlyweight::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function createEmbeddedModel(CommandInterface $command, Parameter $structure, array $data)
    {
        if ($structure->getType() === 'array') {
            // we're actually building an array of models
            return $this->createEmbeddedModelArray($command, $structure, $data);
        }

        if (isset($structure->extends)) {
            $structure->setName($structure->extends);
        }

        $embeddedCommand = $this->factory->factory($command, $data);

        $processedData = $this->process($embeddedCommand, $structure, $data);
        return new Model($processedData, $structure);
    }

    /**
     * Creates an array of models from an embedded resource
     *
     * @param \Guzzle\Service\Command\CommandInterface $command
     * @param \Guzzle\Service\Description\Parameter    $structure
     * @param array                                    $data
     *
     * @return array
     */
    public function createEmbeddedModelArray(CommandInterface $command, Parameter $structure, array $data)
    {
        $items = $structure->getItems();
        $models = array();

        foreach ($data as $element) {
            $models[] = $this->createEmbeddedModel($command, $items, $element);
        }

        return $models;
    }

    /**
     * Processes model data according to a parameter schema
     *
     * @param \Desk\Relationship\Resource\EmbeddedCommand $command
     * @param \Guzzle\Service\Description\Parameter       $schema
     * @param array                                       $data
     *
     * @return array
     */
    public function process(EmbeddedCommand $command, Parameter $schema, array $data)
    {
        $result = array();
        $visitors = array();

        $properties = $schema->getProperties();

        foreach ($properties as $property) {
            $location = $property->getLocation();
            if ($location && !isset($visitors[$location])) {
                // add visitor for this location and trigger before()
                $visitor = $this->visitors->getResponseVisitor($location);
                $visitor->before($command, $result);
                $visitors[$location] = $visitor;
            }
        }

        $response = $command->getResponse();

        // Visit additional properties when it is an actual schema
        $additional = $schema->getAdditionalProperties();
        if ($additional instanceof Parameter) {
            // Only visit when a location is specified
            $location = $additional->getLocation();
            if ($location) {
                if (!isset($visitors[$location])) {
                    $visitors[$location] = $this->visitors->getResponseVisitor($location);
                    $visitors[$location]->before($command, $result);
                }
                // Only traverse if an array was parsed from the before() visitors
                if (is_array($result)) {
                    // Find each additional property
                    foreach (array_keys($result) as $key) {
                        // Check if the model actually knows this property. If so, then it is not additional
                        if (!$schema->getProperty($key)) {
                            // Set the name to the key so that we can parse it with each visitor
                            $additional->setName($key);
                            $visitors[$location]->visit($command, $response, $additional, $result);
                        }
                    }
                    // Reset the additionalProperties name to null
                    $additional->setName(null);
                }
            }
        }

        // Apply the parameter value with the location visitor
        foreach ($properties as $property) {
            $location = $property->getLocation();
            if ($location) {
                $visitors[$location]->visit($command, $response, $property, $result);
            }
        }

        // Call the after() method of each found visitor
        foreach ($visitors as $visitor) {
            $visitor->after($command);
        }

        return $result;
    }
}
