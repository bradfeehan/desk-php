<?php

namespace Desk\Relationship;

use Desk\Relationship\ResponseParser;
use Desk\Relationship\Visitor\Request\JsonVisitor;
use Desk\Relationship\Visitor\Response\EmbeddedVisitor;
use Desk\Relationship\Visitor\Response\LinksVisitor;
use Guzzle\Common\Event;
use Guzzle\Service\Command\DefaultRequestSerializer;
use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\RequestSerializerInterface;
use Guzzle\Service\Command\ResponseParserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Allows models to have relationships according to Desk conventions
 */
class Plugin implements EventSubscriberInterface
{

    /**
     * The ResponseParser to set on newly created commands
     *
     * @var \Guzzle\Service\Command\ResponseParserInterface
     */
    private $parser;

    /**
     * The ResponseParser to set on newly created commands
     *
     * @var \Guzzle\Service\Command\RequestSerializerInterface
     */
    private $serializer;


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array('client.command.create' => 'onCreateCommand');
    }


    /**
     * Allow overriding the ResponseParser that gets set on commands
     *
     * @param \Guzzle\Service\Command\ResponseParserInterface $parser
     * @param \Guzzle\Service\Command\RequestSerializerInterface $serializer
     */
    public function __construct(ResponseParserInterface $parser = null, RequestSerializerInterface $serializer = null)
    {
        $this->parser = $parser;
        $this->serializer = $serializer;
    }

    /**
     * Gets the response parser to set on commands
     *
     * @return \Guzzle\Service\Command\ResponseParserInterface
     */
    public function getResponseParser()
    {
        // @codeCoverageIgnoreStart
        if (!$this->parser) {
            $this->parser = ResponseParser::getInstance();
            $this->parser->addVisitor('links', new LinksVisitor());
            $this->parser->addVisitor('embedded', new EmbeddedVisitor());
        }
        // @codeCoverageIgnoreEnd

        return $this->parser;
    }

    /**
     * Gets the request serializer to set on commands
     *
     * @return \Guzzle\Service\Command\RequestSerializerInterface
     */
    public function getRequestSerializer()
    {
        // @codeCoverageIgnoreStart
        if (!$this->serializer) {
            $visitor = new JsonVisitor();
            $this->serializer = DefaultRequestSerializer::getInstance();
            $this->serializer->addVisitor('json', $visitor);
            $this->serializer->addVisitor('links', $visitor);
        }
        // @codeCoverageIgnoreEnd

        return $this->serializer;
    }

    /**
     * Event listener for the "client.command.create" event
     *
     *     - set up the new command to use the relationship-aware
     *       response parser
     *
     * @param \Guzzle\Common\Event $event Event data, including:
     *     - client:  The client that created the command
     *     - command: The newly-created command
     */
    public function onCreateCommand(Event $event)
    {
        $command = $event['command'];
        if ($command instanceof OperationCommand) {
            $command->setResponseParser($this->getResponseParser());
            $command->setRequestSerializer($this->getRequestSerializer());
        }
    }
}
