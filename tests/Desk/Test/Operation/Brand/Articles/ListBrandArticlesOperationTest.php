<?php

namespace Desk\Test\Operation\Brand\Articles;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListBrandArticlesOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListBrandArticles';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'brand_id';
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


        $one = $articles[0];
        $this->assertSame('Awesome Subject', $one->get('subject'));
        $this->assertSame('<p>Awesome apples</p>', $one->get('body'));
        $this->assertSame(1, $one->get('position'));
        $this->assertTrue($one->get('in_support_center'));

        $oneSelf = $one->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $oneSelf);
        $this->assertSame('ShowArticle', $oneSelf->getName());
        $this->assertSame(1, $oneSelf->get('id'));

        $oneTopic = $one->getLink('topic');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $oneTopic);
        $this->assertSame('ShowTopic', $oneTopic->getName());
        $this->assertSame(1, $oneTopic->get('id'));

        $oneTranslations = $one->getLink('translations');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $oneTranslations);
        $this->assertSame('ListArticleTranslations', $oneTranslations->getName());
        $this->assertSame(1, $oneTranslations->get('article_id'));


        $two = $articles[1];
        $this->assertSame('How to make your customers happy', $two->get('subject'));
        $this->assertSame('<strong>Use Desk.com</strong>', $two->get('body'));
        $this->assertSame(2, $two->get('position'));
        $this->assertTrue($two->get('in_support_center'));

        $twoSelf = $two->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $twoSelf);
        $this->assertSame('ShowArticle', $twoSelf->getName());
        $this->assertSame(2, $twoSelf->get('id'));

        $twoTopic = $two->getLink('topic');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $twoTopic);
        $this->assertSame('ShowTopic', $twoTopic->getName());
        $this->assertSame(1, $twoTopic->get('id'));

        $twoTranslations = $two->getLink('translations');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $twoTranslations);
        $this->assertSame('ListArticleTranslations', $twoTranslations->getName());
        $this->assertSame(2, $twoTranslations->get('article_id'));
    }
}
