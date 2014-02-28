<?php

namespace Desk\Relationship\Resource;

use Desk\Exception\UnexpectedValueException;
use Desk\Relationship\Exception\InvalidLinkFormatException;
use Desk\Relationship\Resource\CommandBuilderInterface;
use Guzzle\Http\QueryString;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Parameter;

class CommandBuilder implements CommandBuilderInterface
{

    /**
     * {@inheritdoc}
     */
    public function createLinkCommand(CommandInterface $command, Parameter $structure, array $data)
    {
        $this->validateLink($data);
        $this->validateLinkStructure($structure);

        $operation = $structure->getData('operation');
        $pattern = $structure->getData('pattern');
        $params = $this->parseHref($data['href'], $pattern);

        return $command->getClient()->getCommand($operation, $params);
    }

    /**
     * Validates link data (from an API response)
     *
     * @param array $data The link data from the API response
     *
     * @throws \Desk\Relationship\Exception\InvalidLinkFormatException
     */
    public function validateLink(array $data)
    {
        if (empty($data['href'])) {
            throw InvalidLinkFormatException::fromLink($data);
        }
    }

    /**
     * Validates the structure of a link (from the service description)
     *
     * @param \Guzzle\Service\Description\Parameter $structure
     *
     * @throws \Desk\Exception\UnexpectedValueException If it's invalid
     */
    public function validateLinkStructure(Parameter $structure)
    {
        if (!$structure->getData('operation')) {
            throw new UnexpectedValueException(
                "Parameter with 'links' location requires 'operation'"
            );
        }

        if (!$structure->getData('pattern')) {
            throw new UnexpectedValueException(
                "Parameter with 'links' location requires 'pattern'"
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
     * @throws \Desk\Exception\UnexpectedValueException If no matches
     * are found or there's an error with the regex
     */
    public function parseHref($href, $pattern)
    {
        $parameters = array();

        // Parse href using pattern
        if (!($result = preg_match($pattern, $href, $parameters))) {
            throw new UnexpectedValueException(
                "Couldn't parse parameters from link href " .
                "(pattern given was '$pattern', href was '$href')"
            );
        }

        // Only return named capture groups -- preg_match() returns
        // both numeric keys for all capture groups, and string keys
        // for named capture groups, so filter out numeric keys
        for ($i = 0; $i < count($parameters); $i++) {
            unset($parameters[$i]);
        }

        // Handle special "_query" parameter
        if (isset($parameters['_query'])) {
            $query = $this->parseQueryString($parameters['_query']);
            $parameters = array_merge($parameters, $query->toArray());
            unset($parameters['_query']);
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

    /**
     * Same as QueryString::fromString, supports comma-separated values
     *
     * @param string $query
     *
     * @return \Guzzle\Http\QueryString
     */
    public function parseQueryString($query)
    {
        $q = new QueryString();

        if (0 !== strlen($query)) {
            if ($query[0] == '?') {
                $query = substr($query, 1);
            }
            foreach (explode('&', $query) as $kvp) {
                $parts = explode('=', $kvp, 2);
                $key = rawurldecode($parts[0]);

                if (array_key_exists(1, $parts)) {
                    if (strpos($parts[1], ',') !== false) {
                        $value = explode(',', $parts[1]);
                        foreach ($value as &$item) {
                            $item = str_replace('+', '%20', $item);
                            $item = rawurldecode($item);
                        }
                    } else {
                        $value = str_replace('+', '%20', $parts[1]);
                        $value = rawurldecode($value);
                    }

                    $q->add($key, $value);
                } else {
                    $q->add($key, '');
                }
            }
        }

        return $q;
    }
}
