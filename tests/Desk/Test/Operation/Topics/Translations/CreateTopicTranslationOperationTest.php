<?php

namespace Desk\Test\Operation\Topics\Translations;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;
use DateTime;
use DateTimeZone;

/**
 * @coversNothing
 * @group system
 */
class CreateTopicTranslationOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateTopicTranslation';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'topic_id' => 1,
            'locale' => 'ja',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $body = '{"name":"Japanese","locale":"ja"}';

        return array(
            array(
                array(
                    'topic_id' => 1,
                    'locale' => 'ja',
                    'name' => 'Japanese',
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
                    'topic_id' => 1,
                )
            ),
            array(
                array(
                    'locale' => 'ja',
                )
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $topicTranslation)
    {
        $this->assertSame('TopicTranslationModel', $topicTranslation->getStructure()->getName());

        $this->assertSame('Japanese', $topicTranslation->get('name'));
        $this->assertSame('ja', $topicTranslation->get('locale'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('topic', 'ShowTopic', array('id' => 1)),
        );
    }
}
