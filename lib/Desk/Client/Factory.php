<?php

namespace Desk\Client;

use Desk\Client;
use Desk\Client\FactoryInterface;
use Desk\Exception\InvalidArgumentException;
use Desk\Relationship\Plugin as RelationshipPlugin;
use Guzzle\Common\Collection;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Service\Description\ServiceDescription;

class Factory implements FactoryInterface
{

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
        $client = new Client($baseUrl, $config);

        $this->addAuthentication($client);
        $this->addServiceDescription($client);
        $this->addRelationshipPlugin($client);

        return $client;
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
        $required = array('authentication');
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

        // Authentication is required, either OAuth or Basic
        // If none specified, try to autodetect it
        if (!isset($config['authentication'])) {
            if ((
                isset($config['username']) &&
                isset($config['password'])
            )) {
                // If username and password are set, use basic auth
                $config['authentication'] = 'basic';
            } elseif ((
                isset($config['consumer_key']) &&
                isset($config['consumer_secret']) &&
                isset($config['token']) &&
                isset($config['token_secret'])
            )) {
                // Otherwise, use OAuth if we have enough data
                $config['authentication'] = 'oauth';
            }
        }

        return Collection::fromConfig($config, $default, $required);
    }

    /**
     * Adds the correct authentication configuration for a client
     *
     * @param Desk\Client $client The client (with configuration)
     *
     * @throws InvalidArgumentException If authentication configuration
     * provided is unknown
     */
    public function addAuthentication(&$client)
    {
        $authentication = $client->getConfig('authentication');
        switch ($authentication) {
            case 'basic':
                $client->setAuth(
                    $client->getConfig('username'),
                    $client->getConfig('password')
                );
                break;
            case 'oauth':
                $client->addSubscriber(
                    new OauthPlugin(
                        array(
                            'consumer_key'    => $client->getConfig('consumer_key'),
                            'consumer_secret' => $client->getConfig('consumer_secret'),
                            'token'           => $client->getConfig('token'),
                            'token_secret'    => $client->getConfig('token_secret'),
                        )
                    )
                );
                break;
            default:
                $value = $client->getConfig('authentication');
                throw new InvalidArgumentException("Invalid authentication '$value'");
        }
    }

    /**
     * Adds the correct service description to a client
     *
     * @param Desk\Client $client The client to add the description to
     */
    public function addServiceDescription(&$client)
    {
        $description = ServiceDescription::factory(__DIR__ . '/desk.json');
        $client->setDescription($description);
    }

    /**
     * Adds a relationship plugin to a client
     *
     * @param Desk\Client $client The client to add the plugin to
     */
    public function addRelationshipPlugin(&$client)
    {
        $client->addSubscriber(new RelationshipPlugin());
    }
}
