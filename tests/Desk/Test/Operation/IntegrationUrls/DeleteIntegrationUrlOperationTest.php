<?php

namespace Desk\Test\Operation\IntegrationUrls;

use Desk\Test\Helper\Operation\DeleteOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class DeleteIntegrationUrlOperationTest extends DeleteOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'DeleteIntegrationUrl';
    }
}
