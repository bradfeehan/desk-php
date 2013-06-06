<?php

namespace Desk\Test\Operation\Articles\Translations;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListArticleTranslationsOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListArticleTranslations';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'article_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $translations)
    {
        foreach ($translations as $translation) {
            $this->assertSame('ArticleTranslationModel', $translation->getStructure()->getName());
        }

        $this->assertSame(2, count($translations));


        $en = $translations[0];
        $this->assertSame('en', $en->get('locale'));
        $this->assertSame('Awesome Subject', $en->get('subject'));
        $this->assertSame('<p>Awesome apples</p>', $en->get('body'));

        $enSelf = $en->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $enSelf);
        $this->assertSame('ShowArticleTranslation', $enSelf->getName());
        $this->assertSame(1, $enSelf->get('article_id'));
        $this->assertSame('en', $enSelf->get('locale'));


        $es = $translations[1];
        $this->assertSame('es', $es->get('locale'));
        $this->assertSame('Spanish Translation', $es->get('subject'));
        $this->assertSame('Traducción español aquí', $es->get('body'));

        $esSelf = $es->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $esSelf);
        $this->assertSame('ShowArticleTranslation', $esSelf->getName());
        $this->assertSame(1, $esSelf->get('article_id'));
        $this->assertSame('es', $esSelf->get('locale'));
    }
}
