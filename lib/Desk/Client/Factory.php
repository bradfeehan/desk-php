<?php

namespace Desk\Client;

use Desk\Client;
use Desk\Client\CommaAggregatorListener;
use Desk\Client\FactoryInterface;
use Desk\Client\ServiceDescriptionLoader;
use Desk\Command\PreValidator;
use Desk\Exception\InvalidArgumentException;
use Desk\RateLimit\Plugin as DeskRateLimitPlugin;
use Desk\Relationship\Plugin as RelationshipPlugin;
use Guzzle\Common\Collection;
use Guzzle\Common\ToArrayInterface;
use Guzzle\Plugin\Backoff\BackoffPlugin;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Service\Description\ServiceDescriptionLoader as GuzzleServiceDescriptionLoader;

class Factory implements FactoryInterface
{

    /**
     * The service description loader used by this factory
     *
     * @var \Guzzle\Service\Description\ServiceDescriptionLoader
     */
    private $loader;


    /**
     * Creates a new Factory
     *
     * @param \Guzzle\Service\Description\ServiceDescriptionLoader $loader
     */
    public function __construct(GuzzleServiceDescriptionLoader $loader = null)
    {
        $this->loader = $loader ?: new ServiceDescriptionLoader();
    }


    /**
     * Factory method to create a new instance of Desk\Client
     *
     * @see \Desk\Client::factory
     *
     * @param array|\Guzzle\Common\Collection $config Configuration options
     *
     * @return \Desk\Client
     */
    public function factory($config = array())
    {
        $config = $this->processConfig($config);
        $baseUrl = isset($config['base_url']) ? $config['base_url'] : null;
        $client = new Client($baseUrl, $config);

        $this->addAuthentication($client);
        $this->addServiceDescription($client);
        $this->addCommaAggregatorListener($client);
        $this->addPreValidator($client);
        $this->addRelationshipPlugin($client);
        $this->addRateLimitPlugin($client);

        return $client;
    }

    /**
     * Processes the configuration passed to self::factory()
     *
     * @param array|\Guzzle\Common\Collection $config
     *
     * @return \Guzzle\Common\Collection
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

        if ($config instanceof ToArrayInterface) {
            $config = $config->toArray();
        }

        return Collection::fromConfig($config, $default, $required);
    }

    /**
     * Adds the correct authentication configuration for a client
     *
     * @param \Desk\Client $client The client (with configuration)
     *
     * @throws \InvalidArgumentException If authentication configuration
     * provided is unknown
     */
    public function addAuthentication(Client &$client)
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
     * @param \Desk\Client $client The client to add the description to
     */
    public function addServiceDescription(Client &$client)
    {
        $description = $this->loader->load(Client::getDescriptionFilename());
        $client->setDescription($description);
    }

    /**
     * Adds a listener so that all created requests use CommaAggregator
     *
     * @param \Desk\Client $client The client to add the listener to
     */
    public function addCommaAggregatorListener(Client &$client)
    {
        $client->addSubscriber(new CommaAggregatorListener());
    }

    /**
     * Adds a PreValidator plugin to the client
     *
     * @param \Desk\Client $client The client to add the PreValidator to
     */
    public function addPreValidator(Client &$client)
    {
        $client->addSubscriber(new PreValidator());
    }

    /**
     * Adds a relationship plugin to the client
     *
     * @param \Desk\Client $client The client to add the Plugin to
     */
    public function addRelationshipPlugin(Client &$client)
    {
        $client->addSubscriber(new RelationshipPlugin());
    }

    /**
     * Adds the rate-limiting plugin to the client
     *
     * @param \Desk\Client $client The client to add the Plugin to
     */
    public function addRateLimitPlugin(Client &$client)
    {
        $client->addSubscriber(new DeskRateLimitPlugin());
    }
}
