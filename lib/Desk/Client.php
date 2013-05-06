<?php

namespace Desk;

use Desk\Client\Factory as ClientFactory;

class Client extends \Guzzle\Service\Client
{

    /**
     * Factory method to create a new instance of this client
     *
     * Available configuration options:
     *   - base_url:    Full base URL
     *   - subdomain:   Desk.com subdomain (if base_url omitted)
     *   - api_version: Desk API version (defaults to 2)
     *
     * @param array|Collection $config Configuration options
     *
     * @return Desk\Client
     */
    public static function factory($config = array())
    {
        return ClientFactory::instance()->factory($config);
    }
}
