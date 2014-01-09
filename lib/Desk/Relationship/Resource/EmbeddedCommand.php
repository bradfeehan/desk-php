<?php

namespace Desk\Relationship\Resource;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Command\AbstractCommand;

class EmbeddedCommand extends AbstractCommand implements CommandInterface
{
    /**
     *
     * @var EmbeddedResponse
     */
    protected $response;

    /**
     *
     * @param EmbeddedResponse $response
     *
     * @return EmbeddedCommand
     */
    public function setResponse(EmbeddedResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     *
     * @return EmbeddedResponse
     */
    public function getResponse()
    {
        return $this->response;
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
    public function isPrepared()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        // do nothing, it will never be called
    }
}
