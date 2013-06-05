<?php

namespace Desk\Test\Operation\Topics\Articles;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListTopicArticlesOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListTopicArticles';
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
    protected function assertSystem(array $articles)
    {
        foreach ($articles as $article) {
            $this->assertSame('ArticleModel', $article->getStructure()->getName());
        }

        $this->assertSame(2, count($articles));


        $apple = $articles[0];
        $this->assertSame('Awesome Subject', $apple->get('subject'));
        $this->assertSame('<p>Awesome apples</p>', $apple->get('body'));
        $this->assertSame(1, $apple->get('position'));
        $this->assertSame(false, $apple->get('in_support_center'));

        $appleSelf = $apple->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $appleSelf);
        $this->assertSame('ShowArticle', $appleSelf->getName());
        $this->assertSame(1, $appleSelf->get('id'));

        $appleTopic = $apple->getLink('topic');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $appleTopic);
        $this->assertSame('ShowTopic', $appleTopic->getName());
        $this->assertSame(1, $appleTopic->get('id'));

        $appleTranslations = $apple->getLink('translations');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $appleTranslations);
        $this->assertSame('ListArticleTranslations', $appleTranslations->getName());
        $this->assertSame(1, $appleTranslations->get('article_id'));


        $banana = $articles[1];
        $this->assertSame('Awesome Subject', $banana->get('subject'));
        $this->assertSame('<p>Awesome bananas</p>', $banana->get('body'));
        $this->assertSame(2, $banana->get('position'));
        $this->assertSame(false, $banana->get('in_support_center'));

        $bananaSelf = $banana->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $bananaSelf);
        $this->assertSame('ShowArticle', $bananaSelf->getName());
        $this->assertSame(2, $bananaSelf->get('id'));

        $bananaTopic = $banana->getLink('topic');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $bananaTopic);
        $this->assertSame('ShowTopic', $bananaTopic->getName());
        $this->assertSame(1, $bananaTopic->get('id'));

        $bananaTranslations = $banana->getLink('translations');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $bananaTranslations);
        $this->assertSame('ListArticleTranslations', $bananaTranslations->getName());
        $this->assertSame(2, $bananaTranslations->get('article_id'));
    }
}
