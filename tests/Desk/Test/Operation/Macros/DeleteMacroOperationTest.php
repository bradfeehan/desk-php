<?php

namespace Desk\Test\Operation\Macros;

use Desk\Test\Helper\Operation\DeleteOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class DeleteMacroOperationTest extends DeleteOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'DeleteMacro';
    }
}
