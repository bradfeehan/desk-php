<?php

namespace Desk\RateLimit;

use Desk\RateLimit\Strategy as DeskRateLimitStrategy;
use Guzzle\Plugin\Backoff\BackoffPlugin;

/**
 * A type of Backoff plugin that handles Desk.com's rate limiting
 */
class Plugin extends BackoffPlugin
{

    public function __construct()
    {
        parent::__construct(new DeskRateLimitStrategy());
    }
}
