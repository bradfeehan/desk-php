<?php

namespace Desk\Test\Operation\Topics;

use Desk\Relationship\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowTopicOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowTopic';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/topics/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $topic)
    {
        $this->assertSame('TopicModel', $topic->getStructure()->getName());

        $this->assertSame('Customer Support', $topic->get('name'));
        $this->assertSame('This is key to going from good to great', $topic->get('description'));
        $this->assertSame(1, $topic->get('position'));
        $this->assertSame(true, $topic->get('allow_questions'));
        $this->assertSame(true, $topic->get('in_support_center'));
        $this->assertInstanceOf('DateTime', $topic->get('created_at'));
        $this->assertSame(1368550802, $topic->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $topic->get('updated_at'));
        $this->assertSame(1368982802, $topic->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('articles', 'ListTopicArticles', array('topic_id' => 1)),
        );
    }
}
