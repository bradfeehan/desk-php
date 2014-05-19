<?php

namespace Desk\Test\Operation\Articles;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\UpdateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class UpdateArticleOperationTest extends UpdateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getOperationName()
    {
        return 'UpdateArticle';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'id' => 1,
            'subject' => 'How to make your customers happy',
            'body' => '<strong>Use Desk.com</strong>',
            'body_email' => 'Custom email body for article',
            'body_email_auto' => false,
            'topic_id' => 2,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $body = '{"subject":"foo","_links":{"topic":{"class":"topic","href":' .
            '"\\\\/api\\\\/v2\\\\/topics\\\\/3"}}}';

        return array(
            array(
                array('id' => 33, 'subject' => 'foo', 'topic_id' => 3),
                array('body' => "#^$body$#"),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array()),
            array(array('subject' => null, 'topic_id' => 2)),
            array(array('subject' => true, 'topic_id' => 2)),
            array(array('subject' => false, 'topic_id' => 2)),
            array(array('subject' => new \stdClass(), 'topic_id' => 2)),
            array(array('subject' => 'a', 'topic_id' => 2)), // too short
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertSystem(Model $article)
    {
        $this->assertSame('ArticleModel', $article->getStructure()->getName());

        $this->assertSame('How to make your customers happy', $article->get('subject'));
        $this->assertSame('<strong>Use Desk.com</strong>', $article->get('body'));
        $this->assertSame('Custom email body for article', $article->get('body_email'));
        $this->assertSame(false, $article->get('body_email_auto'));
        $this->assertSame("Use Desk.com", $article->get('body_chat'));
        $this->assertSame(true, $article->get('body_chat_auto'));
        $this->assertSame("<strong>Use Desk.com</strong>", $article->get('body_web_callback'));
        $this->assertSame(true, $article->get('body_web_callback_auto'));
        $this->assertSame("Use Desk.com", $article->get('body_twitter'));
        $this->assertSame(true, $article->get('body_twitter_auto'));
        $this->assertSame("Use Desk.com", $article->get('body_qna'));
        $this->assertSame(true, $article->get('body_qna_auto'));
        $this->assertSame("Use Desk.com", $article->get('body_phone'));
        $this->assertSame(true, $article->get('body_phone_auto'));
        $this->assertSame("Use Desk.com", $article->get('body_facebook'));
        $this->assertSame(true, $article->get('body_facebook_auto'));
        $this->assertSame(null, $article->get('keywords'));
        $this->assertSame(1, $article->get('position'));
        $this->assertSame(null, $article->get('quickcode'));
        $this->assertSame(null, $article->get('in_support_center'));
        $this->assertSame(null, $article->get('internal_notes'));
        $this->assertSame(null, $article->get('publish_at'));

        $this->assertInstanceOf('DateTime', $article->get('created_at'));
        $this->assertSame(1372451977, $article->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $article->get('updated_at'));
        $this->assertSame(1372452277, $article->get('updated_at')->getTimestamp());
    }

    /**
     * @{inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('topic', 'ShowTopic', array('id' => 2)),
            array(
                'translations',
                'ListArticleTranslations',
                array('article_id' => 1),
            ),
        );
    }
}
