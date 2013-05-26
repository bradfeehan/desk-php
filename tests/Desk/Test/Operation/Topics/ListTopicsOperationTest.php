<?php

namespace Desk\Test\Operation\Topics;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListTopicsOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListTopics';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $topics)
    {
        foreach ($topics as $topic) {
            $this->assertSame('TopicModel', $topic->getStructure()->getName());
        }

        $this->assertSame(2, count($topics));


        $support = $topics[0];
        $this->assertSame('Customer Support', $support->get('name'));
        $this->assertSame('This is key to going from good to great', $support->get('description'));
        $this->assertSame(1, $support->get('position'));
        $this->assertSame(true, $support->get('allow_questions'));
        $this->assertSame(true, $support->get('in_support_center'));

        $supportSelf = $support->getLink('articles');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $supportSelf);
        $this->assertSame('ListTopicArticles', $supportSelf->getName());
        $this->assertSame(1, $supportSelf->get('topic_id'));


        $another = $topics[1];
        $this->assertSame('Another Topic', $another->get('name'));
        $this->assertSame('Not the first one, but another one!', $another->get('description'));
        $this->assertSame(2, $another->get('position'));
        $this->assertSame(true, $another->get('allow_questions'));
        $this->assertSame(true, $another->get('in_support_center'));

        $anotherSelf = $another->getLink('articles');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $anotherSelf);
        $this->assertSame('ListTopicArticles', $anotherSelf->getName());
        $this->assertSame(2, $anotherSelf->get('topic_id'));
    }
}
