<?php

namespace Desk\Relationship\Resource;

use Guzzle\Common\Collection;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Operation;

class EmbeddedCommand implements CommandInterface
{

    /**
     * The client associated with this command
     *
     * @var Guzzle\Service\ClientInterface
     */
    protected $client;

    /**
     * The operation associated with this command
     *
     * @var Guzzle\Service\Description\Operation
     */
    protected $operation;

    /**
     * The request object
     *
     * @var mixed
     */
    protected $request;

    /**
     * The response object
     *
     * @var mixed
     */
    protected $response;

    /**
     * The result object
     *
     * @var mixed
     */
    protected $result;


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getOperation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getOperation()
    {
        if (!$this->operation) {
            $name = 'EmbeddedCommand';
            $this->operation = new Operation(array('name' => $name));
        }

        return $this->operation;
    }

    /**
     * {@inheritdoc}
     */
    public function isExecuted()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the request to use for this command
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrepared()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        return $this->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestHeaders()
    {
        return new Collection();
    }

    /**
     * {@inheritdoc}
     */
    public function setOnComplete($callable)
    {
        // take no action
    }
}
