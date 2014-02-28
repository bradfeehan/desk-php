<?php

namespace Desk\Relationship\Visitor\Request;

use Desk\Relationship\Visitor\DecoratedRequestVisitor;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Command\LocationVisitor\Request\JsonVisitor as GuzzleJsonVisitor;
use Guzzle\Service\Command\LocationVisitor\Request\RequestVisitorInterface;
use Guzzle\Service\Description\Parameter;
use SplObjectStorage;

/**
 * A RequestVisitor to set up the "_links" key in a request JSON body
 *
 * The default request JsonVisitor overwrites the body of the request
 * in its after() method. This means that if another request visitor
 * attempts to modify the body of the request, that data may be lost
 * if the default JsonVisitor runs after it. So to modify the body of a
 * request, it's necessary to override the behaviour of the default
 * JsonVisitor.
 *
 * This RequestVisitor alters the behaviour of the default JsonVisitor
 * using the Decorator pattern. It is then used for both the "json"
 * location as well as the new "links" location. When visiting a
 * particular parameter, if its location is "json", the implementation
 * of the default JsonVisitor is used unchanged.
 *
 * However, if the location of the parameter is "links", then some
 * modifications are made to correctly build the "_links" key of the
 * request's JSON body. In particular, a new parameter is created,
 * representing the full "_links" key in the request. Values for link
 * parameters are stored, and then visited together last.
 *
 * Since the same visitor is used for two locations, the after() method
 * will be called twice, so it needs to expect this and only act once.
 */
class JsonVisitor extends DecoratedRequestVisitor
{

    /**
     * Stores parameters for visited links for a given command object
     *
     * @var \SplObjectStorage
     */
    private $params;

    /**
     * Stores values for visited links for a given command object
     *
     * @var \SplObjectStorage
     */
    private $values;


    /**
     * Allow construction with default arguments
     *
     * {@inheritdoc}
     */
    public function __construct(RequestVisitorInterface $visitor = null)
    {
        if (!$visitor) {
            $visitor = new GuzzleJsonVisitor();
        }

        parent::__construct($visitor);
        $this->params = new SplObjectStorage();
        $this->values = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to behave differently for the "links" location
     */
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        if ($param->getLocation() === 'links') {
            $this->addLinkParam($command, $param);
            $this->addLinkValue($command, $param, $value);
        } else {
            $component = $this->getDecoratedComponent();
            return $component->visit($command, $request, $param, $value);
        }
    }

    /**
     * Prepares and stores the description for a link parameter
     *
     * Prepares and stores the data describing a link parameter, to be
     * later used by getLinksParameter() to describe all the links.
     *
     * @param \Guzzle\Service\Command\CommandInterface $command
     * @param \Guzzle\Service\Description\Parameter    $parameter
     */
    public function addLinkParam(CommandInterface $command, Parameter $parameter)
    {
        $params = array();

        if (isset($this->params[$command])) {
            $params = $this->params[$command];
        }

        // Store parameter definition for later
        $params[$parameter->getName()] = array(
            'name' => $parameter->getName(),
            'description' => $parameter->getDescription(),
            'required' => $parameter->getRequired(),
            'sentAs' => $parameter->getSentAs(),
            'type' => 'object',
            'properties' => array(
                'class' => array(
                    'type' => 'string',
                    'required' => true,
                    'pattern' => '/^[a-z_]+$/',
                ),
                'href' => array(
                    'type' => 'string',
                    'required' => true,
                    'pattern' => '#^/api/v2/#',
                ),
            ),
        );

        $this->params[$command] = $params;
    }

    /**
     * Prepares and stores the value for a link
     *
     * Creates an link object (with "class" and "href") from the Guzzle
     * Parameter describing the link, and the value provided by the
     * user.
     *
     * @param \Guzzle\Service\Command\CommandInterface $command
     * @param \Guzzle\Service\Description\Parameter    $parameter
     * @param mixed                                   $value
     */
    public function addLinkValue(CommandInterface $command, Parameter $parameter, $value)
    {
        $values = array();

        if (isset($this->values[$command])) {
            $values = $this->values[$command];
        }

        $values[$parameter->getName()] = array(
            'class' => $parameter->getData('class'),
            'href' => preg_replace(
                '/{value}/',
                $parameter->getValue($value),
                $parameter->getData('href')
            ),
        );

        $this->values[$command] = $values;
    }

    /**
     * Gets the completed links parameter
     *
     * Constructs a new Guzzle Parameter object, which will have all of
     * the visited links parameters as its properties.
     *
     * @param \Guzzle\Service\Command\CommandInterface $command
     *
     * @return \Guzzle\Service\Description\Parameter
     */
    public function getLinksParameter(CommandInterface $command)
    {
        $params = array();

        if (isset($this->params[$command])) {
            $params = $this->params[$command];
            unset($this->params[$command]);
        }

        return new Parameter(
            array(
                'name' => '_links',
                'location' => 'json',
                'type' => 'object',
                'properties' => $params,
            )
        );
    }

    /**
     * Gets and clears all the values for the links visited
     *
     * Returns the values for the links that have been visited, before
     * clearing the list of visited link values. If there are no link
     * values stored, NULL is returned.
     *
     * @param \Guzzle\Service\Command\CommandInterface $command
     *
     * @return array
     */
    public function getLinksValues(CommandInterface $command)
    {
        $values = null;

        if (isset($this->values[$command])) {
            $values = $this->values[$command];
            unset($this->values[$command]);
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to behave differently if link values are stored.
     */
    public function after(CommandInterface $command, RequestInterface $request)
    {
        $component = $this->getDecoratedComponent();
        $values = $this->getLinksValues($command);

        if ($values) {
            $param = $this->getLinksParameter($command);
            $component->visit($command, $request, $param, $values);
        }

        return $component->after($command, $request);
    }
}
