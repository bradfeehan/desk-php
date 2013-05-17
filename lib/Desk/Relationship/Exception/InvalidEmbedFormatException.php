<?php

namespace Desk\Relationship\Exception;

use Desk\Exception\UnexpectedValueException;

class InvalidEmbedFormatException extends UnexpectedValueException
{

    public static function fromEmbed(array $embed)
    {
        $message = 'Invalid embedded resource format';
        $issues = array();

        if (empty($embed['_links'])) {
            $issues[] = "missing expected '_links' element";
        }

        if (empty($embed['_links']['self'])) {
            $issues[] = "missing expected 'self' link";
        }

        if ($issues) {
            $message .= ': ' . implode('; ', $issues);
        }

        return new static($message);
    }
}
