<?php

namespace Desk\Relationship;

use Desk\Relationship\ResponseParser;
use Guzzle\Common\Event;
use SplObjectStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Allows models to have relationships according to Desk conventions
 */
class Plugin implements EventSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array('client.command.create' => 'onCreateCommand');
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
        $resourceBuilder = new ResourceBuilder($event['client']);
        $responseParser = ResponseParser::getInstance();
        $responseParser->setResourceBuilder($resourceBuilder);
        $event['command']->setResponseParser($responseParser);
    }
}
