<?php

namespace Desk\Test\Operation\Topics;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class CreateTopicOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateTopic';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'name' => 'Social Media',
            'allow_questions' => true,
            'in_support_center' => true,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array(
            array(
                array('name' => 'Social Media', 'allow_questions' => true, 'in_support_center' => true),
                array('body' => '#^{"name":"Social Media","allow_questions":true,"in_support_center":true}$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array()),
            array(array('name' => '')),
            array(array('name' => false)),
            array(array('name' => true)),
            array(array('name' => null)),
            array(array('description' => 'foo')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $topic)
    {
        $this->assertSame('TopicModel', $topic->getStructure()->getName());

        $this->assertSame('Social Media', $topic->get('name'));
        $this->assertSame(null, $topic->get('description'));
        $this->assertSame(1, $topic->get('position'));
        $this->assertSame(true, $topic->get('allow_questions'));
        $this->assertSame(true, $topic->get('in_support_center'));
        $this->assertInstanceOf('DateTime', $topic->get('created_at'));
        $this->assertSame(1374868071, $topic->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $topic->get('updated_at'));
        $this->assertSame(1374868071, $topic->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('articles', 'ListTopicArticles', array('topic_id' => 1)),
            array('translations', 'ListTopicTranslations', array('topic_id' => 1)),
        );
    }
}
