<?php

namespace Desk\Exception;

use Desk\Exception as DeskException;

class BadMethodCallException extends \BadMethodCallException implements DeskException
{
}
