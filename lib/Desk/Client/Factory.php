<?php

namespace Desk\Client;

use Desk\Client;
use Guzzle\Common\Collection;

class Factory
{

    /**
     * The singleton instance of the client factory
     *
     * @var Desk\Client\Factory
     */
    private static $instance;


    /**
     * Gets the singleton instance
     *
     * @return Desk\Client\Factory
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Overrides the singleton instance (for dependency injection)
     *
     * Sets the instance back to default if no argument is supplied.
     *
     * @param Desk\Client\Factory $instance The new instance to use
     */
    public static function setInstance(Factory $instance = null)
    {
        self::$instance = $instance;
    }


    /**
     * Factory method to create a new instance of Desk\Client
     *
     * @see Desk\Client::factory
     *
     * @param array|Collection $config Configuration options
     *
     * @return Desk\Client
     */
    public function factory($config = array())
    {
        $config = $this->processConfig($config);
        $baseUrl = isset($config['base_url']) ? $config['base_url'] : null;
        return new Client($baseUrl, $config);
    }

    /**
     * Processes the configuration passed to self::factory()
     *
     * @param array|Guzzle\Common\Collection $config
     *
     * @return Guzzle\Common\Collection
     */
    public function processConfig($config)
    {
        $required = array();
        $default = array(
            'api_version' => 2,
            'base_url' => 'https://{subdomain}.desk.com/api/v{api_version}/',
        );

        $baseUrl = $default['base_url'];
        if (isset($config['base_url'])) {
            $baseUrl = $config['base_url'];
        }

        // Subdomain is required, if the base URL is the default (not
        // set), or if it contains "{subdomain}" in it
        if (strpos($baseUrl, '{subdomain}') !== false) {
            $required[] = 'subdomain';
        }

        return Collection::fromConfig($config, $default, $required);
    }
}
