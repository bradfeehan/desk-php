<?php

namespace Desk\Test\Operation\Articles;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class SearchArticlesOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'SearchArticles';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('text' => 'foobar'),
                array('query' => '#^text=foobar$#')
            ),
            array(
                array('text' => '!@#$%^&*()'),
                array('query' => '#^text=%21%40%23%24%25%5E%26%2A%28%29$#')
            ),
            array(
                array('topic_ids' => array(1, 2, 3)),
                array('query' => '#^topic_ids=1,2,3$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalidAdditional()
    {
        return array(
            // true, false, null, 0, -12, 12.3, -12.3, '3', new \stdClass(),
            array(array('text' => true)),
            array(array('text' => false)),
            array(array('text' => 12.3)),
            array(array('text' => new \stdClass())),
            array(array('topic_ids' => true)),
            array(array('topic_ids' => false)),
            array(array('topic_ids' => 0)),
            array(array('topic_ids' => -12)),
            array(array('topic_ids' => 12.3)),
            array(array('topic_ids' => -12.3)),
            array(array('topic_ids' => '3')),
            array(array('topic_ids' => new \stdClass())),
            array(array('topic_ids' => array('1'))),
            array(array('topic_ids' => array(true))),
            array(array('topic_ids' => array(1, true))),
        );
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
        $this->assertSame(true, $apple->get('in_support_center'));

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


        $happy = $articles[1];
        $this->assertSame('How to make your customers happy', $happy->get('subject'));
        $this->assertSame('<strong>Use Desk.com</strong>', $happy->get('body'));
        $this->assertSame(2, $happy->get('position'));
        $this->assertSame(false, $happy->get('in_support_center'));

        $happySelf = $happy->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $happySelf);
        $this->assertSame('ShowArticle', $happySelf->getName());
        $this->assertSame(2, $happySelf->get('id'));

        $happyTopic = $happy->getLink('topic');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $happyTopic);
        $this->assertSame('ShowTopic', $happyTopic->getName());
        $this->assertSame(1, $happyTopic->get('id'));

        $happyTranslations = $happy->getLink('translations');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $happyTranslations);
        $this->assertSame('ListArticleTranslations', $happyTranslations->getName());
        $this->assertSame(2, $happyTranslations->get('article_id'));
    }
}
