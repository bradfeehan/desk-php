<?php

namespace Desk\Test\Operation\Cases;

use Desk\Relationship\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCaseOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCase';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/cases/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $case)
    {
        $this->assertSame('CaseModel', $case->getStructure()->getName());

        $this->assertSame('Welcome', $case->get('subject'));
        $this->assertSame(5, $case->get('priority'));
        $this->assertSame('new', $case->get('status'));
        $this->assertSame(array('Spam', 'Test'), $case->get('labels'));
        $this->assertInstanceOf('DateTime', $case->get('created_at'));
        $this->assertSame(1335994728, $case->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $case->get('updated_at'));
        $this->assertSame(1335994728, $case->get('updated_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $case->get('received_at'));
        $this->assertSame(1335994728, $case->get('received_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('customer', 'ShowCustomer', array('id' => 1)),
            array('assigned_user', 'ShowUser', array('id' => 2)),
            array('assigned_group', 'ShowGroup', array('id' => 1)),
        );
    }
}
