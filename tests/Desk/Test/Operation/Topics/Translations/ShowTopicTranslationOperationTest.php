<?php

namespace Desk\Test\Operation\Topics\Translations;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowTopicTranslationOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowTopicTranslation';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('topic_id');
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to include locale parameter
     */
    protected function getExampleParameters(array $overrides = array())
    {
        return array_merge(
            parent::getExampleParameters($overrides),
            array('locale' => 'en_us')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('topic_id' => 4, 'locale' => 'abc'),
                array('url' => '#/topics/4/translations/abc$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $topic)
    {
        $this->assertSame('TopicTranslationModel', $topic->getStructure()->getName());

        $this->assertSame('Customer Support', $topic->get('name'));
        $this->assertSame('en_us', $topic->get('locale'));
        $this->assertInstanceOf('DateTime', $topic->get('created_at'));
        $this->assertSame(1369511651, $topic->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $topic->get('updated_at'));
        $this->assertSame(1369943651, $topic->get('updated_at')->getTimestamp());
    }
}
