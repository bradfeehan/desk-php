<?php

namespace Desk;

use Desk\Client\FactoryInterface;
use Desk\Client\Factory;

class Client extends \Guzzle\Service\Client
{

    /**
     * The client factory that creates instances of this class
     *
     * @var Desk\Client\FactoryInterface
     */
    private static $factory;


    /**
     * Gets the client factory that creates instances of this class
     *
     * @return Desk\Client\FactoryInterface
     */
    public static function getFactory()
    {
        if (!self::$factory) {
            self::$factory = new Factory();
        }

        return self::$factory;
    }

    /**
     * Sets the client factory that creates instances of this class
     *
     * If called with no argument, this will reset the client factory
     * to an instance of the default Desk\Client\Factory.
     *
     * @param Desk\Client\FactoryInterface $factory
     */
    public static function setFactory(FactoryInterface $factory = null)
    {
        self::$factory = $factory;
    }

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
        return self::getFactory()->factory($config);
    }


    /**
     * Sets basic authentication details on all subsequent requests
     *
     * @param string $user     The basic auth username
     * @param string $password The basic auth password
     *
     * @return Desk\Client
     * @chainable
     */
    public function setAuth($user, $password)
    {
        return $this->addDefaultHeader('Authorization', 'Basic ' . base64_encode("$user:$password"));
    }

    /**
     * Appends to the list of default headers, don't replace them all
     *
     * @param string $header The name of the header
     * @param string $value  The value to set the header to
     *
     * @return Desk\Client
     * @chainable
     */
    public function addDefaultHeader($header, $value)
    {
        $headers = $this->getDefaultHeaders() ?: new Collection();
        $headers->set($header, $value);
        $this->setDefaultHeaders($headers);

        return $this;
    }
}
