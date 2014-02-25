<?php

namespace Desk\Relationship\Resource;

use Desk\Relationship\Resource\EmbeddedCommand;
use Desk\Relationship\Resource\EmbeddedCommandFactoryInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Service\Command\CommandInterface;

class EmbeddedCommandFactory implements EmbeddedCommandFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function factory(CommandInterface $originalCommand, array $data)
    {
        $command = $this->newCommand();

        // set up embedded command
        $command->setClient($originalCommand->getClient());

        $originalResponse = $originalCommand->getResponse();
        $response = $this->createResponse($originalResponse, $data);
        $command->setResponse($response);

        return $command;
    }

    /**
     * Creates an EmbeddedCommand instance
     *
     * @return Desk\Relationship\Resource\EmbeddedCommand
     */
    public function newCommand()
    {
        return new EmbeddedCommand();
    }

    /**
     * Prepares an EmbeddedResponse from the original response and data
     *
     * @param Guzzle\Http\Message\Response $originalResponse
     * @param array                        $data
     *
     * @return Desk\Relationship\Resource\EmbeddedResponse
     */
    public function createResponse(Response $originalResponse, array $data)
    {
        $statusCode = $originalResponse->getStatusCode();
        $reasonPhrase = $originalResponse->getReasonPhrase();
        $headers = $originalResponse->getHeaders()->toArray();
        $body = json_encode($data);

        // set reason phrase -- needs to be done vie
        $response = $this->newResponse($statusCode, $headers, $body);
        $response->setReasonPhrase($reasonPhrase);

        return $response;
    }

    /**
     * Creates an EmbeddedResponse instance
     *
     * @return Desk\Relationship\Resource\EmbeddedResponse
     */
    public function newResponse($statusCode, $headers = null, $body = null)
    {
        return new EmbeddedResponse($statusCode, $headers, $body);
    }
}
