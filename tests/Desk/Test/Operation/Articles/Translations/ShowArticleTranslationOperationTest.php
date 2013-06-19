<?php

namespace Desk\Test\Operation\Articles\Translations;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowArticleTranslationOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowArticleTranslation';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('article_id');
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
            array('locale' => 'en')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('article_id' => 4, 'locale' => 'abc'),
                array('url' => '#/articles/4/translations/abc$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $article)
    {
        $this->assertSame('ArticleTranslationModel', $article->getStructure()->getName());

        $this->assertSame('en', $article->get('locale'));
        $this->assertSame('Awesome Subject', $article->get('subject'));
        $this->assertSame('<p>Awesome apples</p>', $article->get('body'));
        $this->assertInstanceOf('DateTime', $article->get('publish_at'));
        $this->assertSame(1370375351, $article->get('publish_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $article->get('created_at'));
        $this->assertSame(1370375051, $article->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $article->get('updated_at'));
        $this->assertSame(1370375351, $article->get('updated_at')->getTimestamp());
    }
}
