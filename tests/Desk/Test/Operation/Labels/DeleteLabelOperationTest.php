<?php

namespace Desk\Test\Operation\Labels;

use Desk\Test\Helper\Operation\DeleteOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class DeleteLabelOperationTest extends DeleteOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'DeleteLabel';
    }
}
