<?php

namespace Desk\RateLimit;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\HttpException;
use Guzzle\Plugin\Backoff\BackoffStrategyInterface;

/**
 * A BackoffStrategyInterface that implements Desk.com's rate limiting
 */
class Strategy implements BackoffStrategyInterface
{

    /**
     * Stores the multiplier for the timeout
     *
     * Any time period reported from Desk will be scaled by this factor
     * before being used.
     *
     * @var float
     */
    private $multiplier;


    /**
     * Creates a new Strategy instance for a given multiplier
     *
     * @param float $multiplier Scaling factor for Desk.com times
     */
    public function __construct($multiplier = 1)
    {
        $this->multiplier = (float) $multiplier;
    }

    /**
     * Retrieves the time multiplier
     *
     * @return float
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * Get the amount of time to delay in seconds before retrying a request
     *
     * @param int              $retries  Number of retries of the request
     * @param RequestInterface $request  Request that was sent
     * @param Response         $response Response that was received. Note that there may not be a response
     * @param HttpException    $e        Exception that was encountered if any
     *
     * @return bool|int Returns false to not retry or the number of seconds to delay between retries
     */
    public function getBackoffPeriod(
        $retries,
        RequestInterface $request,
        Response $response = null,
        HttpException $e = null
    ) {
        if (!$response) {
            return false;
        }

        if ($response->getStatusCode() != 429) {
            return false;
        }

        $reset = (string) $response->getHeader('X-Rate-Limit-Reset');

        if (!preg_match('/^[0-9]+$/', $reset)) {
            return false;
        }

        return (((integer) $reset) + 0.1) * $this->getMultiplier();
    }
}
