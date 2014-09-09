<?php

namespace Desk\Test\Operation\Cases\Attachments;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCaseAttachmentOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCaseAttachment';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('case_id', 'attachment_id');
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('case_id' => 4, 'attachment_id' => 3),
                array('url' => '#/cases/4/attachments/3$#')
            ),
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
