<?php

namespace Desk\Relationship;

use Desk\Client;
use Desk\Exception\InvalidArgumentException;
use Desk\Relationship\Exception\InvalidEmbedFormatException;
use Desk\Relationship\Exception\InvalidLinkFormatException;
use Desk\Relationship\Model;
use Desk\Relationship\ResourceBuilderInterface;

class ResourceBuilder implements ResourceBuilderInterface
{

    /**
     * The client used to build resources
     *
     * @var Desk\Client
     */
    private $client;


    /**
     * @param Desk\Client $client The client used to build resources
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function createCommandFromLink(array $link)
    {
        $this->validateLink($link);

        try {
            $command = $this->getCommandForDeskClass($link['class']);
        } catch (InvalidArgumentException $e) {
            throw new InvalidLinkFormatException(
                "Unknown linked resource class '{$link['class']}'"
            );
        }

        $command->setUri($link['href']);

        return $command;
    }

    /**
     * {@inheritdoc}
     */
    public function createModelFromEmbedded(array $data)
    {
        // detect if this is an embedded array of models
        if (isset($data[0])) {
            $models = array();

            foreach ($data as $element) {
                $models[] = $this->createModelFromEmbedded($element);
            }

            return $models;
        }

        if (empty($data['_links']) || empty($data['_links']['self'])) {
            throw InvalidEmbedFormatException::fromEmbed($data);
        }

        $this->validateLink($data['_links']['self']);
        $class = $data['_links']['self']['class'];

        try {
            $structure = $this->getModelForDeskClass($class);
        } catch (InvalidArgumentException $e) {
            throw new InvalidEmbedFormatException(
                "Unknown embedded resource class '$class'"
            );
        }

        // TODO: ResponseParser::visitResult() should go over $data first
        return new Model($this, $data, $structure);
    }

    /**
     * Validates a link array
     *
     * @param array $link
     *
     * @throws Desk\Relationship\Exception\InvalidLinkFormatException
     */
    public function validateLink(array $link)
    {
        if (empty($link['class']) || empty($link['href'])) {
            throw InvalidLinkFormatException::fromLink($link);
        }
    }

    /**
     * Creates a command for a particular Desk "class"
     *
     * @param string $deskClass
     *
     * @return Desk\Relationship\Command
     *
     * @throws Desk\Exception\InvalidArgumentException If $deskClass is
     * not a known Desk class
     */
    public function getCommandForDeskClass($deskClass)
    {
        $operations = $this->client->getDescription()->getOperations();
        foreach ($operations as $operation) {
            if ($operation->getData('deskClass') === $deskClass) {
                return $this->client->getCommand($operation->getName());
            }
        }

        throw new InvalidArgumentException(
            "Unknown Desk class '$deskClass'"
        );
    }

    /**
     * Gets the model structure for a particular Desk "class"
     *
     * @param string $deskClass
     *
     * @return Guzzle\Service\Description\Parameter
     *
     * @throws Desk\Exception\InvalidArgumentException If $deskClass is
     * not a known Desk class
     */
    public function getModelForDeskClass($deskClass)
    {
        $models = $this->client->getDescription()->getModels();
        foreach ($models as $model) {
            if ($model->getData('deskClass') === $deskClass) {
                return $model;
            }
        }

        throw new InvalidArgumentException(
            "Unknown Desk class '$deskClass'"
        );
    }
}
