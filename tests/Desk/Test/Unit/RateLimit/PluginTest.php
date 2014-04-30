<?php

namespace Desk\Test\Unit\RateLimit;

use Desk\Test\Helper\UnitTestCase;
use Desk\RateLimit\Plugin as RateLimitPlugin;

class PluginTest extends UnitTestCase
{

    protected function getMockedClass()
    {
        return 'Desk\\RateLimit\\Plugin';
    }

    public function testConstruct()
    {
        $plugin = new RateLimitPlugin();

        $this->assertInstanceOf(
            'Guzzle\\Plugin\\Backoff\\BackoffPlugin',
            $plugin
        );

        $this->assertInstanceOf($this->getMockedClass(), $plugin);
    }

    public function testHasCorrectStrategy()
    {
        $plugin = new RateLimitPlugin();
        $this->assertInstanceOf(
            'Desk\\RateLimit\\Strategy',
            $this->getPrivateProperty($plugin, 'strategy')
        );
    }
}
