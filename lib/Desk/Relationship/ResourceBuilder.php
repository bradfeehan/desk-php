<?php

namespace Desk\Relationship;

use Desk\Exception\InvalidArgumentException;
use Desk\Exception\UnexpectedValueException;
use Desk\Relationship\Exception\InvalidLinkFormatException;
use Desk\Relationship\Model;
use Desk\Relationship\ResourceBuilderInterface;
use Guzzle\Service\Command\AbstractCommand;

class ResourceBuilder implements ResourceBuilderInterface
{

    /**
     * The command which this builder is making resources for
     *
     * @var Guzzle\Service\Command\AbstractCommand
     */
    private $command;


    /**
     * @param Guzzle\Service\Command\AbstractCommand $command Command being built for
     */
    public function __construct(AbstractCommand $command)
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    public function createCommandFromLink($name, array $data, array $description)
    {
        $this->validateLink($data);
        $this->validateLinkDescription($description);

        $params = $this->parseHref($data['href'], $description['pattern']);
        return $this->command->getClient()->getCommand($description['operation'], $params);
    }

    /**
     * {@inheritdoc}
     */
    public function createModelFromEmbedded($name, array $data, array $description)
    {
        $this->validateEmbedDescription($description);

        // recursively process numerical-indexed arrays into an array
        // of models created by this same function
        if (isset($data[0])) {
            $models = array();

            foreach ($data as $element) {
                $models[] = $this->createModelFromEmbedded(
                    $name,
                    $element,
                    $description
                );
            }

            return $models;
        }

        $structure = $this->command
            ->getClient()
            ->getDescription()
            ->getModel($description['model']);

        // TODO: ResponseParser::visitResult() should go over $data first
        return new Model($this, $data, $structure);
    }

    /**
     * Validates a link array (from the API)
     *
     * @param array $data The link data from the API response
     *
     * @throws Desk\Relationship\Exception\InvalidLinkFormatException
     */
    public function validateLink(array $link)
    {
        if (empty($link['href'])) {
            throw InvalidLinkFormatException::fromLink($link);
        }
    }

    /**
     * Validates a link description (from the model description)
     *
     * @param array $description The link description from the model
     *
     * @throws Desk\Exception\UnexpectedValueException If it's invalid
     */
    public function validateLinkDescription(array $description)
    {
        if (empty($description['operation'])) {
            throw new UnexpectedValueException(
                "Missing operation for link description"
            );
        }

        if (empty($description['pattern'])) {
            throw new UnexpectedValueException(
                "Missing pattern for link description"
            );
        }
    }

    /**
     * Validates an embed description (from the model description)
     *
     * @param array $description The embed description from the model
     *
     * @throws Desk\Exception\UnexpectedValueException If it's invalid
     */
    public function validateEmbedDescription(array $description)
    {
        if (empty($description['model'])) {
            throw new UnexpectedValueException(
                "Missing model for embed description"
            );
        }
    }

    /**
     * Parses the href into an associative array of parameters
     *
     * @param string $href
     * @param string $pattern
     *
     * @return array
     * @throws Desk\Exception\UnexpectedValueException If no matches
     * are found or there's an error with the regex
     */
    public function parseHref($href, $pattern)
    {
        // Parse href using pattern
        if (!($result = preg_match($pattern, $href, $parameters))) {
            throw new UnexpectedValueException(
                "Couldn't parse parameters from link href"
            );
        }

        // Only return named capture groups -- preg_match() returns
        // both numeric keys for all capture groups, and string keys
        // for named capture groups, so filter out numeric keys
        for ($i = 0; $i < count($parameters); $i++) {
            unset($parameters[$i]);
        }

        // Convert strings containing integers to integer types. This
        // is desirable, because if the service description says that
        // the type must be a string, SchemaValidator will cast a
        // numeric type to a string, and validation will pass. But if
        // the schema specifies an integer type, then a string
        // containing a number is passed in, it will fail validation.
        foreach ($parameters as $key => &$value) {
            if (is_string($value) && ctype_digit($value)) {
                $value = (integer) $value;
            }
        }

        return $parameters;
    }
}
