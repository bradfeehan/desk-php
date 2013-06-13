<?php

namespace Desk\Relationship;

use Desk\Exception\InvalidArgumentException;
use Desk\Exception\UnexpectedValueException;
use Desk\Relationship\Exception\InvalidLinkFormatException;
use Desk\Relationship\Model;
use Desk\Relationship\ResourceBuilderInterface;
use Guzzle\Http\QueryString;
use Guzzle\Service\Client;

class ResourceBuilder implements ResourceBuilderInterface
{

    /**
     * The client used to create link commands
     *
     * @var Guzzle\Service\Client
     */
    private $client;


    /**
     * @param Guzzle\Service\Client $client Used to create link models
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function createCommandFromLink($name, array $data, array $description)
    {
        $this->validateLink($data);
        $this->validateLinkDescription($description);

        $params = $this->parseHref($data['href'], $description['pattern']);
        return $this->client->getCommand($description['operation'], $params);
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

        $structure = $this->client->getDescription()
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
     * @return Guzzle\Http\QueryString
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
