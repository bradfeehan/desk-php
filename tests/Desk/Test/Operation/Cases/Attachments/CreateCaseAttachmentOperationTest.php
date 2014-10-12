<?php

namespace Desk\Test\Operation\Cases\Attachments;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;
use DateTime;
use DateTimeZone;

/**
 * @coversNothing
 * @group system
 */
class CreateCaseAttachmentOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateCaseAttachment';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'case_id' => '1',
            'file_name' => 'awesome_pic.png',
            'content_type' => 'image/png',
            'content' => 'base64encodedcontent',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $body = '{"content_type":"image\\\\/png","file_name":"awesome_pic.png","content":"base64encodedcontent"}';

        return array(
            array(
                array(
                    'case_id' => '1',
                    'content_type' => 'image/png',
                    'file_name' => 'awesome_pic.png',
                    'content' => 'base64encodedcontent',
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
            array(array()),
            array(array('file_name' => 'a')),
            array(array('content_type' => 'b')),
            array(array('content' => 'c')),
            array(array('file_name' => 'a', 'content_type' => 'b')),
            array(array('content_type' => 'b', 'content' => 'c')),
            array(array('file_name' => 'a', 'content' => 'c')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $attachment)
    {
        $this->assertSame('CaseAttachmentModel', $attachment->getStructure()->getName());

        $this->assertSame('awesome_pic.png', $attachment->get('file_name'));
        $this->assertSame('image/png', $attachment->get('content_type'));
        $this->assertSame('500', $attachment->get('size'));
        $this->assertSame('http://example.com/short_lived_link_to_the_file_content', $attachment->get('url'));
        $this->assertInstanceOf('DateTime', $attachment->get('created_at'));
        $this->assertSame(1409340260, $attachment->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $attachment->get('updated_at'));
        $this->assertSame(1409340260, $attachment->get('updated_at')->getTimestamp());
        $this->assertNull($attachment->get('erased_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('case', 'ShowCase', array('id' => 1)),
        );
    }
}
