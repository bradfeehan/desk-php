<?php

namespace Desk\Test\Operation\Articles\Translations;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;
use DateTime;
use DateTimeZone;

/**
 * @coversNothing
 * @group system
 */
class CreateArticleTranslationOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateArticleTranslation';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'article_id' => 1,
            'locale' => 'es',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $body = '{"locale":"es","subject":"Spanish"}';

        return array(
            array(
                array(
                    'article_id' => 1,
                    'locale' => 'es',
                    'subject' => 'Spanish',

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
                    'article_id' => 1,
                )
            ),
            array(
                array(
                    'locale' => 'es',
                )
            ),
            array(
                array(
                    'article_id' => 1,
                    'locale' => 'es',
                    'outdated' => 'foo',
                )
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $articleTranslation)
    {
        $this->assertSame('ArticleTranslationModel', $articleTranslation->getStructure()->getName());

        $this->assertSame('Spanish Translation', $articleTranslation->get('subject'));
        $this->assertSame('es', $articleTranslation->get('locale'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('article', 'ShowArticle', array('id' => 1)),
        );
    }
}
