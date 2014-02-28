<?php

namespace Desk\Relationship\Visitor;

use Desk\Common\Decorator;
use Guzzle\Service\Command\LocationVisitor\Request\RequestVisitorInterface;

/**
 * A RequestVisitor which decorates another RequestVisitor
 *
 * This RequestVisitor is a decorator which can wrap an existing
 * instance of another RequestVisitor, and modify or add additional
 * functionality. The existing RequestVisitor should be passed as the
 * first argument to the constructor.
 */
abstract class DecoratedRequestVisitor extends Decorator implements RequestVisitorInterface
{

    /**
     * Overridden to provide type-checking of the decorated component
     *
     * @param \Guzzle\Service\Command\LocationVisitor\Request\RequestVisitorInterface $visitor
     */
    public function __construct(RequestVisitorInterface $visitor)
    {
        parent::__construct($visitor);
    }
}
