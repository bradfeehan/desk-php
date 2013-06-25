<?php

namespace Desk\Relationship;

use Guzzle\Http\Message\Response;
use Guzzle\Service\Command\AbstractCommand;
use Guzzle\Service\Command\LocationVisitor\VisitorFlyweight;
use Guzzle\Service\Command\OperationResponseParser;
use Guzzle\Service\Description\OperationInterface;
use ReflectionClass;

/**
 * A relationship-aware version of the default OperationResponseParser
 */
class ResponseParser extends OperationResponseParser
{

    /**
     * The model class to instantiate
     *
     * @var string
     */
    const MODEL_CLASS = 'Desk\\Relationship\\Resource\\Model';


    /**
     * Cached singleton instance of this class
     *
     * This class needs to store its singleton instance separately to
     * its parent class' instance, otherwise one will be inaccessible
     * (if the other one is instantiated first).
     *
     * @var Desk\Relationship\ResponseParser
     */
    private static $relationshipInstance;


    /**
     * {@inheritdoc}
     *
     * Overridden to store this class separately to the parent class,
     * because otherwise an instance of this child class will be
     * retrieved by a call to the parent method (which is undesirable).
     *
     * @return Desk\Relationship\ResponseParser
     */
    public static function getInstance()
    {
        if (!self::$relationshipInstance) {
            $instance = new self(VisitorFlyweight::getInstance());
            self::$relationshipInstance = $instance;
        }

        return self::$relationshipInstance;
    }

    /**
     * {@inheritdoc}
     */
    protected function handleParsing(AbstractCommand $command, Response $response, $contentType)
    {
        // Only use overridden behaviour if response type is a model
        if (!$this->responseTypeIsModel($command)) {
            return parent::handleParsing($command, $response, $contentType);
        }

        // Preparation of model data (this is same as parent class)
        $structure = $command
            ->getOperation()
            ->getServiceDescription()
            ->getModel($command->getOperation()->getResponseClass());

        $data = $this->visitResult($structure, $command, $response);

        return $this->createClass(
            static::MODEL_CLASS,
            array($data, $structure)
        );
    }

    /**
     * Creates an instance of a class
     *
     * Optionally, constructor arguments may be passed in as an array
     * using the second parameter to this function. The first element
     * in the array will be the first constructor argument, the second
     * element will be the second argument, etc.
     *
     * @param string $className The class to construct
     * @param array  $arguments Optional constructor arguments
     *
     * @return mixed
     */
    public function createClass($className, array $arguments = array())
    {
        $class = new ReflectionClass($className);
        return $class->newInstanceArgs($arguments);
    }

    /**
     * Determines whether a command's response type is a model
     *
     * @param Guzzle\Service\Command\AbstractCommand $command
     *
     * @return boolean
     */
    public function responseTypeIsModel(AbstractCommand $command)
    {
        $operation = $command->getOperation();
        $processing = $command->get(AbstractCommand::RESPONSE_PROCESSING);
        $description = $operation->getServiceDescription();

        return
            $operation->getResponseType() == OperationInterface::TYPE_MODEL &&
            $description->hasModel($operation->getResponseClass()) &&
            $processing == AbstractCommand::TYPE_MODEL;
    }
}
