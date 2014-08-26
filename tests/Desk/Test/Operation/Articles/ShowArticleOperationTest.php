<?php

namespace Desk\Test\Operation\Articles;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowArticleOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowArticle';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/articles/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $article)
    {
        $this->assertSame('ArticleModel', $article->getStructure()->getName());

        $this->assertSame('Awesome Subject', $article->get('subject'));
        $this->assertSame('<p>Awesome apples</p>', $article->get('body'));
        $this->assertSame(1, $article->get('position'));
        $this->assertSame(false, $article->get('in_support_center'));
        $this->assertInstanceOf('DateTime', $article->get('publish_at'));
        $this->assertSame(1369414502, $article->get('publish_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $article->get('created_at'));
        $this->assertSame(1369414202, $article->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $article->get('updated_at'));
        $this->assertSame(1369414502, $article->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('topic', 'ShowTopic', array('id' => 1)),
            array('translations', 'ListArticleTranslations', array('article_id' => 1)),
            array('brands', 'ShowBrand', array('id' => 1)),
        );
    }
}
