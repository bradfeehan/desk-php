<?php

namespace Desk\Iterator;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Resource\ResourceIterator as GuzzleResourceIterator;

class ResourceIterator extends GuzzleResourceIterator
{

    /**
     * @var \Guzzle\Service\Command\CommandInterface Next command
     */
    protected $nextCommand;


    /**
     * {@inheritdoc}
     */
    protected function sendRequest()
    {
        if ($this->nextCommand) {
            $this->command = $this->nextCommand;
        }

        $result = $this->command->execute();

        if ($result->hasLink('next')) {
            $this->nextToken = (string) true;
            $this->nextCommand = $result->getLink('next');
        } else {
            $this->nextToken = null;
            $this->nextCommand = null;
        }

        return $result->getEmbedded('entries');
    }
}
