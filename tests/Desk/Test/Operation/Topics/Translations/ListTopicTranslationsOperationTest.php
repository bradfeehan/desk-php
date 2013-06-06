<?php

namespace Desk\Test\Operation\Topics\Translations;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListTopicTranslationsOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListTopicTranslations';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'topic_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $translations)
    {
        foreach ($translations as $translation) {
            $this->assertSame('TopicTranslationModel', $translation->getStructure()->getName());
        }

        $this->assertSame(2, count($translations));


        $enUs = $translations[0];
        $this->assertSame('Customer Support', $enUs->get('name'));
        $this->assertSame('en_us', $enUs->get('locale'));

        $enUsSelf = $enUs->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $enUsSelf);
        $this->assertSame('ShowTopicTranslation', $enUsSelf->getName());
        $this->assertSame(1, $enUsSelf->get('topic_id'));
        $this->assertSame('en_us', $enUsSelf->get('locale'));


        $ja = $translations[1];
        $this->assertSame('Japanese Translation', $ja->get('name'));
        $this->assertSame('ja', $ja->get('locale'));

        $jaSelf = $ja->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $jaSelf);
        $this->assertSame('ShowTopicTranslation', $jaSelf->getName());
        $this->assertSame(1, $jaSelf->get('topic_id'));
        $this->assertSame('ja', $jaSelf->get('locale'));
    }
}
