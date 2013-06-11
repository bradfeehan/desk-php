<?php

namespace Desk\Test\Operation\Topics\Translations;

use Desk\Test\Helper\Operation\DeleteOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class DeleteTopicTranslationOperationTest extends DeleteOperationTestCase
{

    /**
     * {@inheritdoc}
     *
     * Overridden to include topic_id and locale parameters.
     */
    protected function getExampleParameters(array $overrides = array())
    {
        return array_merge(
            array('topic_id' => 1, 'locale' => 'abc'),
            $overrides
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array()),
            array($this->getExampleParameters(array('topic_id' => true))),
            array($this->getExampleParameters(array('topic_id' => false))),
            array($this->getExampleParameters(array('topic_id' => null))),
            array($this->getExampleParameters(array('topic_id' => 0))),
            array($this->getExampleParameters(array('topic_id' => -12))),
            array($this->getExampleParameters(array('topic_id' => 12.3))),
            array($this->getExampleParameters(array('topic_id' => -12.3))),
            array($this->getExampleParameters(array('topic_id' => '3'))),
            array($this->getExampleParameters(
                array('topic_id' => new \stdClass())
            )),
            array($this->getExampleParameters(array('locale' => true))),
            array($this->getExampleParameters(array('locale' => false))),
            array($this->getExampleParameters(array('locale' => null))),
            array($this->getExampleParameters(
                array('topic_id' => new \stdClass())
            )),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'DeleteTopicTranslation';
    }
}
