<?php

namespace Desk\Relationship\Resource;

use Guzzle\Http\Message\Response;

class EmbeddedResponse extends Response
{

    /**
     * Sets the reason phrase (in the HTTP status line) directly
     *
     * @param string $reasonPhrase
     *
     * @return Desk\Relationship\Resource\EmbeddedResponse
     */
    public function setReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
    }
}
