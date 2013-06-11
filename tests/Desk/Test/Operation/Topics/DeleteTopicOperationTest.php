<?php

namespace Desk\Test\Operation\Topics;

use Desk\Test\Helper\Operation\DeleteOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class DeleteTopicOperationTest extends DeleteOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'DeleteTopic';
    }
}
