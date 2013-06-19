<?php

namespace Desk\Relationship\Exception;

use Desk\Exception as DeskException;
use OutOfBoundsException;

class UnknownResourceException extends OutOfBoundsException implements DeskException
{
}
