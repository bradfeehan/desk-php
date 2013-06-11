<?php

namespace Desk\Test\Operation\Articles;

use Desk\Test\Helper\Operation\DeleteOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class DeleteArticleOperationTest extends DeleteOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'DeleteArticle';
    }
}
