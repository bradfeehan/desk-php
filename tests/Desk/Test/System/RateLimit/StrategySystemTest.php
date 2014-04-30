<?php

namespace Desk\Test\System\RateLimit;

use Desk\RateLimit\Strategy;
use Desk\Test\Helper\SystemTestCase;
use Guzzle\Http\Client;
use Guzzle\Plugin\Backoff\BackoffPlugin;

class StrategySystemTest extends SystemTestCase
{

    public function testLimited()
    {
        // Create a script to return a 429 response like Desk.com
        $this->getServer()->flush();
        $this->getServer()->enqueue(array(
            "HTTP/1.1 429 Rate limit exceeded\r\nX-Rate-Limit-Reset: 1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 200 OK\r\nContent-Length: 4\r\n\r\ndata"
        ));

        $plugin = new BackoffPlugin($this->strategy());
        $client = new Client($this->getServer()->getUrl());

        $client->getEventDispatcher()->addSubscriber($plugin);
        $request = $client->get();
        $request->send();

        // Make sure it eventually completed successfully
        $this->assertEquals(200, $request->getResponse()->getStatusCode());
        $this->assertEquals('data', $request->getResponse()->getBody(true));

        // Check that two requests were made to retry this request
        $this->assertEquals(2, count($this->getServer()->getReceivedRequests(false)));
        $this->assertEquals(1, $request->getParams()->get(BackoffPlugin::RETRY_PARAM));
    }

    public function testNotLimited()
    {
        // Create a script to return a 429 response like Desk.com
        $this->getServer()->flush();
        $this->getServer()->enqueue(array(
            "HTTP/1.1 200 OK\r\nContent-Length: 4\r\n\r\ndata"
        ));

        $plugin = new BackoffPlugin($this->strategy());
        $client = new Client($this->getServer()->getUrl());

        $client->getEventDispatcher()->addSubscriber($plugin);
        $request = $client->get();
        $request->send();

        // Make sure it eventually completed successfully
        $this->assertEquals(200, $request->getResponse()->getStatusCode());
        $this->assertEquals('data', $request->getResponse()->getBody(true));

        // Check that two requests were made to retry this request
        $this->assertEquals(1, count($this->getServer()->getReceivedRequests(false)));
        $this->assertEquals(0, $request->getParams()->get(BackoffPlugin::RETRY_PARAM));
    }

    public function strategy()
    {
        return new Strategy(0.01);
    }
}
