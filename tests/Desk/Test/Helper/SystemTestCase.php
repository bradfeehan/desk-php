<?php

namespace Desk\Test\Helper;

use Desk\Test\Helper\TestCase;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Service\Client;

abstract class SystemTestCase extends TestCase
{

    /**
     * Adds a LogPlugin to a Guzzle service client object
     *
     * This will cause any requests created by the client to log all
     * requests sent and received.
     *
     * @param Guzzle\Service\Client $client The Guzzle service client
     */
    protected function logClient(Client $client)
    {
        $client->addSubscriber(LogPlugin::getDebugPlugin());
    }
}
