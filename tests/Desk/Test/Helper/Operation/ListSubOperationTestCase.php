<?php

namespace Desk\Test\Helper\Operation;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * Base class for any sub-List* operation tests
 *
 * This is used for ListArticleTranslations, for example -- operations
 * which retrieve a list of sub-items from a parent item.
 *
 * Subclasses must only implement the abstract assertSystem() method.
 * The testParameterValid() and testParameterInvalid() data providers
 * have been set up with parameter values that should be fine for all
 * List operations.
 *
 * To add further test cases for testParameterValid() or
 * testParameterInvalid() in a subclass, simply override
 * dataParameterValidAdditional() and dataParameterInvalidAdditional()
 * respectively.
 */
abstract class ListSubOperationTestCase extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $id = $this->getIdProperty();

        return array_merge(
            array(
                array(array($id => 1)),
                array(array($id => 9, 'page' => 2), array('query' => '#^page=2$#')),
                array(array($id => 10, 'per_page' => 3), array('query' => '#^per_page=3$#')),
                array(array($id => 200, 'page' => 4, 'per_page' => 5), array('query' => '#^per_page=5&page=4$#')),
            ),
            $this->dataParameterValidAdditional()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        $id = $this->getIdProperty();

        return array_merge(
            array(
                array(array()),
                array(array('page' => 4)),
                array(array('per_page' => 5)),
                array(array('page' => 6, 'per_page' => 7)),
                array(array($id => 3, 'page' => true)),
                array(array($id => 3, 'page' => false)),
                array(array($id => 3, 'page' => 0)),
                array(array($id => 3, 'page' => -1)),
                array(array($id => 3, 'page' => 1.4)),
                array(array($id => 3, 'page' => -2.5)),
                array(array($id => 3, 'page' => '8')),
                array(array($id => 3, 'page' => new \stdClass())),
                array(array($id => 3, 'per_page' => true)),
                array(array($id => 3, 'per_page' => false)),
                array(array($id => 3, 'per_page' => 0)),
                array(array($id => 3, 'per_page' => -1)),
                array(array($id => 3, 'per_page' => 3.4)),
                array(array($id => 3, 'per_page' => -7.3)),
                array(array($id => 3, 'per_page' => '5')),
                array(array($id => 3, 'per_page' => new \stdClass())),
            ),
            $this->dataParameterInvalidAdditional()
        );
    }

    /**
     * Gets the command ready for the system test
     *
     * @return Guzzle\Service\Command\AbstractCommand
     */
    protected function getSystemTestCommand()
    {
        $command = parent::getSystemTestCommand();
        $command->set($this->getIdProperty(), 1);
        return $command;
    }

    /**
     * Gets the name of the property containing the main object's ID
     *
     * For example, if the operation is ListArticleTranslation, this
     * function should return "article_id", as the "main object" is
     * the article (which is having its translations listed).
     *
     * @return string
     */
    abstract protected function getIdProperty();
}
