<?php

namespace Desk\Relationship\Visitor\Request;

use Desk\Relationship\Visitor\DecoratedRequestVisitor;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Command\LocationVisitor\Request\JsonVisitor as GuzzleJsonVisitor;
use Guzzle\Service\Command\LocationVisitor\Request\RequestVisitorInterface;
use Guzzle\Service\Description\Parameter;

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
 * request's JSON body. In particular, the parameter used is modified
 * before being passed to the default JsonVisitor's implementation.
 *
 * Since the same visitor is used for two locations, the after() method
 * will be called twice, so it needs to expect this and only act once.
 */
class JsonVisitor extends DecoratedRequestVisitor
{

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
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to behave differently for the "links" location
     */
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        if ($param->getLocation() === 'links') {
            $value = $this->createLinkValue($param, $value);
            $param = $this->createLinkParameter($param);
        }

        $component = $this->getDecoratedComponent();
        return $component->visit($command, $request, $param, $value);
    }

    /**
     * Creates a parameter representing a link
     *
     * @param Guzzle\Service\Description\Parameter $originalParameter
     *
     * @return Guzzle\Service\Description\Parameter
     */
    public function createLinkParameter(Parameter $originalParameter)
    {
        return new Parameter(
            array(
                'name' => $originalParameter->getName(),
                'description' => $originalParameter->getDescription(),
                'required' => $originalParameter->getRequired(),
                'sentAs' => $originalParameter->getSentAs(),
                'location' => 'json',
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
            )
        );
    }

    /**
     * Creates the value for a link
     *
     * @param Guzzle\Service\Description\Parameter $parameter
     * @param mixed                                $originalValue
     *
     * @return array
     */
    public function createLinkValue(Parameter $parameter, $originalValue)
    {
        return array(
            'class' => $parameter->getData('class'),
            'href' => preg_replace(
                '#{value}#',
                $parameter->getValue($originalValue),
                $parameter->getData('href')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function after(CommandInterface $command, RequestInterface $request)
    {
        return $this->getDecoratedComponent()->after($command, $request);
    }
}
