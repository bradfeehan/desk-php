<?php

namespace Desk\Test\Operation\Cases\Attachments;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCaseAttachmentsOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCaseAttachments';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'case_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $attachments)
    {
        foreach ($attachments as $attachment) {
            $this->assertSame('CaseAttachmentModel', $attachment->getStructure()->getName());
        }

        $this->assertSame(2, count($attachments));


        $first = $attachments[0];
        $this->assertSame('awesome_pic.png', $first->get('file_name'));

        $firstSelf = $first->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $firstSelf);
        $this->assertSame('ShowCaseAttachment', $firstSelf->getName());
        $this->assertSame(1, $firstSelf->get('case_id'));
        $this->assertSame(1, $firstSelf->get('attachment_id'));


        $second = $attachments[1];
        $this->assertSame('another_awesome_pic.png', $second->get('file_name'));

        $secondSelf = $second->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $secondSelf);
        $this->assertSame('ShowCaseAttachment', $secondSelf->getName());
        $this->assertSame(1, $secondSelf->get('case_id'));
        $this->assertSame(2, $secondSelf->get('attachment_id'));
    }
}
