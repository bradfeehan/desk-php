<?php

namespace Desk\Relationship\Visitor\Response;

use Desk\Relationship\Visitor\ResponseVisitor;
use Guzzle\Http\Message\Response;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;

/**
 * TODO
 */
abstract class AbstractVisitor extends ResponseVisitor
{

    /**
     * {@inheritdoc}
     */
    public function before(CommandInterface $command, array &$result)
    {
        $json = $command->getResponse()->json();

        // store links to use later
        if (array_key_exists($this->getOutputFieldName(), $json)) {
            $this->set(
                $command,
                $this->getFieldName(),
                $json[$this->getOutputFieldName()]
            );
        }

        // create new array of links which visit() adds to
        $result[$this->getOutputFieldName()] = array();
    }

    /**
     * {@inheritdoc}
     */
    public function visit(CommandInterface $command, Response $response, Parameter $param, &$value, $context = null)
    {
        // check if there's embedded resource data for the parameter
        $resourceData = $this->get($command, $this->getFieldName());
        if (!empty($resourceData[$param->getWireName()])) {
            // create the resource representing the embedded resource data
            $resource = $this->createResourceFromData(
                $command,
                $param,
                $resourceData[$param->getWireName()]
            );

            // store the created embedded resource in the results array
            $value[$this->getOutputFieldName()][$param->getName()] = $resource;
        }
    }

    /**
     * Retrieves the field name for this visitor
     *
     * This is the name of the field where the relevant data is stored for this
     * visitor.
     *
     * @return string
     */
    abstract protected function getFieldName();

    /**
     * Retrieves the field for this visitor to use in the output
     *
     * This just prepends an underscore to the input field name.
     *
     * @return string
     */
    private function getOutputFieldName()
    {
        return '_' . $this->getFieldName();
    }

    /**
     * Creates a resource from resource data, extracted from the response
     *
     * This takes the resource data for a particular parameter, and returns an
     * object representing that resource data.
     *
     * @return object|array
     */
    abstract protected function createResourceFromData(CommandInterface $command, Parameter $structure, array $data);
}
