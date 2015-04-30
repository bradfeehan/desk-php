<?php

namespace Desk\Test\Operation\Cases\History;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

class ShowCaseHistoryOperation extends ShowOperationTestCase
{
    /**
     * The name of the operation to be tested
     *
     * This should be one of the keys under "operation" in the client's
     * service description.
     *
     * @return string
     */
    protected function getOperationName()
    {
        return 'ShowCaseHistory';
    }

    /**
     * Contains assertions to make about the results of the system test
     *
     * @param array $model Resulting model from system test
     */
    protected function assertSystem(Model $model)
    {
        $this->assertEquals('CaseHistoryModel', $model->getStructure()->getName());

        $this->assertEquals(2351, $model->get('id'));
        $this->assertTrue($model->hasLink('self'));
        $this->assertContains(2441978, $model->getPath('changes/*/to'));
        $this->assertInstanceOf('\DateTime', $model->get('created_at'));
    }
}
