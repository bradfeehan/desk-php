<?php

namespace Desk\Iterator;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Resource\ResourceIterator as GuzzleResourceIterator;

class ResourceIterator extends GuzzleResourceIterator
{

    /**
     * {@inheritdoc}
     */
    protected function sendRequest()
    {
        if ($this->nextToken) {
            $this->command = $this->nextToken;
        }

        $result = $this->command->execute();

        if ($result->hasLink('next')) {
            $this->nextToken = $result->getLink('next');
        } else {
            $this->nextToken = null;
        }

        return $result->getEmbedded('entries');
    }
}
