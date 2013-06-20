<?php

namespace Desk\Relationship;

use Desk\Relationship\ResponseParser;
use Desk\Relationship\Visitor\Response\EmbeddedVisitor;
use Desk\Relationship\Visitor\Response\LinksVisitor;
use Guzzle\Common\Event;
use Guzzle\Service\Command\OperationCommand;
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
     * @var Guzzle\Service\Command\ResponseParserInterface
     */
    private $parser;


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
     * @param Guzzle\Service\Command\ResponseParserInterface $parser
     */
    public function __construct(ResponseParserInterface $parser = null)
    {
        $this->parser = $parser;
    }

    /**
     * Gets the response parser to set on commands
     *
     * @return Guzzle\Service\Command\ResponseParserInterface
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
     * Event listener for the "client.command.create" event
     *
     *     - set up the new command to use the relationship-aware
     *       response parser
     *
     * @param Guzzle\Common\Event $event Event data, including:
     *     - client:  The client that created the command
     *     - command: The newly-created command
     */
    public function onCreateCommand(Event $event)
    {
        $command = $event['command'];
        if ($command instanceof OperationCommand) {
            $command->setResponseParser($this->getResponseParser());
        }
    }
}
