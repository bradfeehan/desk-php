<?php

namespace Desk\Test\Operation\Cases;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\UpdateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class UpdateCaseOperationTest extends UpdateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'UpdateCase';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'id' => 1,
            'subject' => 'Welcome',
            'type' => 'email',
            'status' => 'closed',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array(
            array(array('id' => 1), array('body' => '#^$#')),
            array(
                array('id' => 1, 'subject' => 'Welcome'),
                array('body' => '#^{"subject":"Welcome"}$#'),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array('subject' => false)),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $case)
    {
        $this->assertSame('CaseModel', $case->getStructure()->getName());

        $this->assertSame(1, $case->get('id'));
        $this->assertSame('Welcome', $case->get('subject'));
        $this->assertSame(5, $case->get('priority'));
        $this->assertSame('closed', $case->get('status'));
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
