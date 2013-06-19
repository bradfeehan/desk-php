<?php

namespace Desk\Test\Operation\Cases\Message;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCaseMessageOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCaseMessage';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('case_id');
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('case_id' => 4),
                array('url' => '#/cases/4/message$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $reply)
    {
        $this->assertSame('CaseMessageModel', $reply->getStructure()->getName());

        $this->assertSame('Please help', $reply->get('subject'));
        $this->assertSame('Help me with my issue!', $reply->get('body'));
        $this->assertSame('in', $reply->get('direction'));
        $this->assertSame('pending', $reply->get('status'));
        $this->assertSame('john.doe@example.com', $reply->get('to'));
        $this->assertSame('doe.john@example.com', $reply->get('from'));
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
