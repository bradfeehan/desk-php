<?php

namespace Desk\Test\Helper;

use Desk\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 * @group system
 */
abstract class OperationTestCase extends SystemTestCase
{

    /**
     * Gets the client specified in self::getClientName()
     *
     * @return Guzzle\Service\ClientInterface
     */
    final public function client()
    {
        return $this->getServiceBuilder()->get($this->getClientName());
    }

    /**
     * @dataProvider dataParameterValid
     *
     * @param array $parameters Parameter names => parameter values
     * @param array $assertions Assertions to make after setting value.
     *    If this parameter is omitted, the test will just assert that
     *    the request is created correctly. Valid keys include:
     *     - uri:        A regex to apply against the resulting request
     *                   object's URL.
     *     - query:      A regex to apply against the resulting request
     *                   object's query string.
     *     - postFields: A regex to apply against the resulting request
     *                   object's post fields.
     */
    public function testParameterValid(array $parameters, array $assertions = null)
    {
        $request = $this->client()
            ->getCommand($this->getOperationName(), $parameters)
            ->prepare();

        $requestInterface = 'Guzzle\\Http\\Message\\RequestInterface';
        $this->assertInstanceOf($requestInterface, $request);

        if (isset($assertions['url'])) {
            $this->assertRegExp($assertions['url'], (string) $request->getUrl());
        }

        if (isset($assertions['query'])) {
            $this->assertRegExp($assertions['query'], (string) $request->getQuery());
        }

        if (isset($assertions['postFields'])) {
            $this->assertRegExp($assertions['postFields'], (string) $request->getPostFields());
        }
    }

    /**
     * @dataProvider dataParameterInvalid
     * @expectedException Guzzle\Service\Exception\ValidationException
     */
    public function testParameterInvalid(array $parameters)
    {
        $this->client()
            ->getCommand($this->getOperationName(), $parameters)
            ->prepare();
    }

    /**
     * The name of the client to be tested
     *
     * This should be one of the keys under "services" in the service
     * description used for the tests.
     *
     * @return string
     */
    protected function getClientName()
    {
        return 'mock';
    }

    /**
     * The name of the operation to be tested
     *
     * This should be one of the keys under "operation" in the client's
     * service description.
     *
     * @return string
     */
    abstract protected function getOperationName();

    /**
     * Provides data for testParameterValid
     *
     * Should return an array in the following format:
     *
     * array(
     *   array($parameters1, $assertions1),
     *   array($parameters2, $assertions2),
     *   // ...
     * );
     *
     * Where $parameters1 and $parameters2 are parameters to set on the
     * command, which should pass validation. These should be an
     * associative array of parameters to set.
     *
     * The test will be ran once for each element in the root of the
     * array (e.g. twice in the example above).
     *
     * Assertions passed in via the second optional parameter will be
     * tested. Assertions can be specified as an array with the
     * following keys:
     *     - url:        Regex to test the URL (excluding query string)
     *     - query:      Regex to test the query string
     *     - postFields: Regex to test the postField query string
     *
     * If an exception is thrown or one of the regexes don't match, the
     * test will fail.
     *
     * @return array
     */
    abstract public function dataParameterValid();

    /**
     * Provides data for testParameterInvalid
     *
     * Should return an array in the following format:
     *
     * array(
     *   array($parameters1),
     *   array($parameters2),
     *   // ...
     * );
     *
     * Where $parameters1 and $parameters2 are parameters to set on the
     * command, which should fail validation. These should be an
     * associative array of parameters to set.
     *
     * The test will be ran once for each element in the root of the
     * array (e.g. twice in the example above).
     *
     * The parameter with key $name1 will be set to $value1, and the
     * Command should throw a ValidationException. If not, the test
     * will fail.
     *
     * @return array
     */
    abstract public function dataParameterInvalid();

    /**
     * Provides a complete example of using this operation
     */
    abstract public function testSystem();
}
