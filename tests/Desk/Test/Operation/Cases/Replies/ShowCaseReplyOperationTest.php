<?php

namespace Desk\Test\Operation\Cases\Replies;

use Desk\Relationship\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCaseReplyOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCaseReply';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('case_id', 'reply_id');
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('case_id' => 4, 'reply_id' => 5),
                array('url' => '#/cases/4/replies/5$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $reply)
    {
        $this->assertSame('CaseReplyModel', $reply->getStructure()->getName());

        $this->assertSame('Re: Please help', $reply->get('subject'));
        $this->assertSame('Thanks for your question. The answer is 42.', $reply->get('body'));
        $this->assertSame('out', $reply->get('direction'));
        $this->assertSame('pending', $reply->get('status'));
        $this->assertSame('doe.john@example.com', $reply->get('to'));
        $this->assertSame('john.doe@example.com', $reply->get('from'));
        $this->assertInstanceOf('DateTime', $reply->get('created_at'));
        $this->assertSame(1335994728, $reply->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $reply->get('updated_at'));
        $this->assertSame(1335994728, $reply->get('updated_at')->getTimestamp());
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
