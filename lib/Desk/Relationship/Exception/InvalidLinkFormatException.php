<?php

namespace Desk\Relationship\Exception;

use Desk\Exception\UnexpectedValueException;

class InvalidLinkFormatException extends UnexpectedValueException
{

    public static function fromLink(array $link)
    {
        $message = 'Invalid resource link format';
        $issues = array();

        if (empty($link['class'])) {
            $issues[] = "missing expected 'class' element";
        }

        if (empty($link['href'])) {
            $issues[] = "missing expected 'href' element";
        }

        if ($issues) {
            $message .= ': ' . implode('; ', $issues);
        }

        return new static($message);
    }
}
