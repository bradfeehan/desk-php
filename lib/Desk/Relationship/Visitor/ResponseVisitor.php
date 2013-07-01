<?php

namespace Desk\Relationship\Visitor;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Command\LocationVisitor\Response\AbstractResponseVisitor;
use SplObjectStorage;

abstract class ResponseVisitor extends AbstractResponseVisitor
{

    /**
     * Stores data for commands in the process of being parsed
     *
     * @var SplObjectStorage
     */
    private $data;


    /**
     * Stores a value for a particular command and key
     *
     * This can be later retrieved using $this->get($command, $key),
     * where $command and $key are the same as passed to this function.
     *
     * @param Guzzle\Service\Command\CommandInterface $command
     * @param string                                  $key
     * @param mixed                                   $value
     *
     * @return Desk\Relationship\Visitor\ResponseVisitor static
     * @chainable
     */
    public function set(CommandInterface $command, $key, $value)
    {
        if (!$this->data) {
            $this->data = new SplObjectStorage();
        }

        if (isset($this->data[$command])) {
            $data = $this->data[$command];
        } else {
            $data = array();
        }

        $data[$key] = $value;
        $this->data[$command] = $data;

        return $this;
    }

    /**
     * Retrieves a value for a particular command and key
     *
     * @param Guzzle\Service\Command\CommandInterface $command
     * @param string                                  $key
     *
     * @return mixed
     */
    public function get(CommandInterface $command, $key)
    {
        if (!$this->data) {
            return null;
        }

        if (empty($this->data[$command])) {
            return null;
        }

        if (!array_key_exists($key, $this->data[$command])) {
            return null;
        }

        return $this->data[$command][$key];
    }

    /**
     * {@inheritdoc}
     *
     * Clean up any data stored for this command
     */
    public function after(CommandInterface $command)
    {
        if (isset($this->data[$command])) {
            unset($this->data[$command]);
        }
    }
}
