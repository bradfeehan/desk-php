<?php

namespace Desk\Common;

use Desk\Exception\BadMethodCallException;
use Desk\Exception\InvalidArgumentException;

/**
 * A generic implementation of the Decorator pattern
 *
 * Subclasses of this class can define methods with modified or
 * additional functionality to apply to the decorated object. Any calls
 * which should go to the decorated object should be called on
 * "parent". For example, the decorated object's implementation for
 * foo() can be called using parent::foo().
 *
 * Any calls to methods not defined on the decorated object will throw
 * an exception of type Desk\Exception\BadMethodCallException (which is
 * a subclass of \BadMethodCallException, the type of exception that
 * PHP would normally throw in the same circumstance if the object
 * wasn't decorated). The message of the exception will also explain
 * that the object has been decorated.
 */
class Decorator
{

    /**
     * The object being decorated
     *
     * @var object
     */
    private $component;


    /**
     * This constructor can be overridden in a subclass to do
     * type-checking of the $component parameter, to ensure that only
     * objects of a given class are decorated.
     *
     * @param object $component The class being decorated
     */
    public function __construct($component)
    {
        if (!is_object($component)) {
            $message = "Cannot decorate " . gettype($component);
            throw new InvalidArgumentException($message);
        }

        $this->component = $component;
    }

    /**
     * Retrieves the object being decorated
     *
     * @return object
     */
    public function getDecoratedComponent()
    {
        return $this->component;
    }

    /**
     * Pass any method calls down to the decorated component
     *
     * @param string $name      The name of the called method
     * @param array  $arguments Array of method call function arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $callback = array($this->getDecoratedComponent(), $name);

        if (!is_callable($callback)) {
            $decorator = get_called_class();
            $component = get_class($this->getDecoratedComponent());
            throw new BadMethodCallException(
                "Call to undefined method {$decorator}::{$name}() " .
                "(not defined on decorator or decorated $component)"
            );
        }

        return call_user_func_array($callback, $arguments);
    }
}
