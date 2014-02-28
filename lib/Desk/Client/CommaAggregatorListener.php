<?php

namespace Desk\Client;

use Guzzle\Common\Event;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Request;
use Guzzle\Http\QueryAggregator\CommaAggregator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Makes a client use CommaAggregator for all its created requests
 *
 * Any requests created by a client which has this as a subscriber will
 * have their query string use CommaAggregator as the aggregator. This
 * means that all query string items which have multiple values will
 * have a comma-separated value.
 *
 * This only sets the default QueryAggregator, but it can always be
 * manually changed (using $request->getQuery()->setAggregator()).
 */
class CommaAggregatorListener implements EventSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ClientInterface::CREATE_REQUEST => 'setQueryAggregator',
        );
    }

    /**
     * Changes a request's query string aggregator to CommaAggregator
     *
     * Listens to the 'client.create_request' event.
     *
     * @param \Guzzle\Common\Event $event
     */
    public function setQueryAggregator(Event $event)
    {
        $request = $event['request'];
        if ($request instanceof Request) {
            $request->getQuery()->setAggregator(new CommaAggregator());
        }
    }
}
