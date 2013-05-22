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
    public function createCommandFromLink($linkName, array $data)
    {
        $this->validateLink($data);
        $description = $this->getLinkDescription($linkName);

        $parameters = $this->parseHref($data['href'], $description['pattern']);

        return $this->command
            ->getClient()
            ->getCommand($description['operation'], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function createModelFromEmbedded($linkName, array $data)
    {
        // Process an array recursively if it's not associative
        if (isset($data[0])) {
            $models = array();

            foreach ($data as $element) {
                $models[] = $this->createModelFromEmbedded($linkName, $element);
            }

            return $models;
        }

        // find out what model it should be from the link name
        $description = $this->getEmbedDescription($linkName);

        $structure = $this->command
            ->getClient()
            ->getDescription()
            ->getModel($description['model']);

        if (!$structure) {
            throw new UnexpectedValueException(
                "Unknown embedded resource model '{$description['model']}'"
            );
        }

        // TODO: ResponseParser::visitResult() should go over $data first
        return new Model($this, $data, $structure);
    }

    /**
     * Validates a link array
     *
     * @param array $link
     *
     * @throws Desk\Relationship\Exception\InvalidLinkFormatException
     */
    public function validateLink(array $link)
    {
        if (empty($link['class']) || empty($link['href'])) {
            throw InvalidLinkFormatException::fromLink($link);
        }
    }

    /**
     * Finds the link description for a given link name
     *
     * @param string $linkName The name of the link
     *
     * @return array The link description
     * @throws Desk\Exception\InvalidArgumentException If description
     * for the link is missing
     */
    public function getLinkDescription($linkName)
    {
        return $this->getDescription('links', $linkName);
    }

    /**
     * Finds the embeddable link description for a given link name
     *
     * @param string $linkName The name of the link
     *
     * @return array The embeddable link description
     * @throws Desk\Exception\InvalidArgumentException If description
     * for the link is missing
     */
    public function getEmbedDescription($linkName)
    {
        return $this->getDescription('embeds', $linkName);
    }

    /**
     * Helper function to get a link description
     *
     * Implements common functionality of getLinkDescription() and
     * getEmbeddableLinkDescription().
     *
     * @param string $linkType The data key to look for the description
     * @param string $linkName The name of the link
     *
     * @return array
     * @throws Desk\Exception\InvalidArgumentException If description
     * for the link is missing
     */
    public function getDescription($linkType, $linkName)
    {
        $links = $this->command->getOperation()->getData($linkType);

        foreach ((array) $links as $name => $description) {
            if ($name === $linkName) {
                return $description;
            }
        }

        $operation = $this->command->getOperation()->getName();

        throw new InvalidArgumentException(
            "Operation '$operation' missing '$linkName' link description"
        );
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
