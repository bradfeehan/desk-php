<?php

namespace Desk\Test\Operation\Cases\Replies;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;
use DateTime;
use DateTimeZone;

/**
 * @coversNothing
 * @group system
 */
class CreateCaseReplyOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateCaseReply';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'case_id' => 1,
            'body' => 'My Reply',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $date = new DateTime('2012-05-01T21:38:48Z', new DateTimeZone('UTC'));
        $body = '{"direction":"in","body":"The body","created_at":' .
            '"2012-05-01T21:38:48Z","status":"sent"}';

        return array(
            array(
                array(
                    'case_id' => 1,
                    'body' => 'The body',
                    'direction' => 'in',
                    'status' => 'sent',
                    'created_at' => $date,
                ),
                array('body' => "#^$body$#")
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(
                array(
                    'case_id' => 1,
                    'direction' => 'lol',
                )
            ),
            array(
                array(
                    'direction' => 'in',
                )
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $caseReply)
    {
        $this->assertSame('CaseReplyModel', $caseReply->getStructure()->getName());

        $this->assertSame('Re: Please help', $caseReply->get('subject'));
        $this->assertSame('My Reply', $caseReply->get('body'));
        $this->assertSame('out', $caseReply->get('direction'));
        $this->assertSame('pending', $caseReply->get('status'));
        $this->assertSame('doe.john@example.com', $caseReply->get('to'));
        $this->assertSame('john.doe@example.com', $caseReply->get('from'));
        $this->assertNull($caseReply->get('cc'));
        $this->assertNull($caseReply->get('bcc'));
        $this->assertSame('desk_portal', $caseReply->get('client_type'));
        $this->assertSame(1396293604, $caseReply->get('created_at')->getTimestamp());
        $this->assertSame(1396293604, $caseReply->get('updated_at')->getTimestamp());
        $this->assertNull($caseReply->get('hidden_at'));
        $this->assertFalse($caseReply->get('hidden'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('case', 'ShowCase', array('id' => 1)),
        );
    }
}
