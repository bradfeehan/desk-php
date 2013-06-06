<?php

namespace Desk\Test\Operation\Cases\Replies;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCaseRepliesOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCaseReplies';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'case_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $replies)
    {
        foreach ($replies as $reply) {
            $this->assertSame('CaseReplyModel', $reply->getStructure()->getName());
        }

        $this->assertSame(2, count($replies));


        $in = $replies[0];
        $this->assertSame('Please help', $in->get('subject'));
        $this->assertSame('Help me with my issue!', $in->get('body'));
        $this->assertSame('in', $in->get('direction'));
        $this->assertSame('pending', $in->get('status'));

        $inSelf = $in->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $inSelf);
        $this->assertSame('ShowCaseReply', $inSelf->getName());
        $this->assertSame(1, $inSelf->get('case_id'));
        $this->assertSame(1, $inSelf->get('reply_id'));


        $out = $replies[1];
        $this->assertSame('Re: Please help', $out->get('subject'));
        $this->assertSame('Thanks for your question. The answer is 42.', $out->get('body'));
        $this->assertSame('out', $out->get('direction'));
        $this->assertSame('pending', $out->get('status'));

        $outSelf = $out->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $outSelf);
        $this->assertSame('ShowCaseReply', $outSelf->getName());
        $this->assertSame(1, $outSelf->get('case_id'));
        $this->assertSame(2, $outSelf->get('reply_id'));
    }
}
