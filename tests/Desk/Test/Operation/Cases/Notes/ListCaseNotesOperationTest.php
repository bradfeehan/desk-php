<?php

namespace Desk\Test\Operation\Cases\Notes;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCaseNotesOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCaseNotes';
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
    protected function assertSystem(array $notes)
    {
        foreach ($notes as $note) {
            $this->assertSame('CaseNoteModel', $note->getStructure()->getName());
        }

        $this->assertSame(2, count($notes));


        $please = $notes[0];
        $this->assertSame('Please assist me with this case', $please->get('body'));

        $pleaseSelf = $please->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $pleaseSelf);
        $this->assertSame('ShowCaseNote', $pleaseSelf->getName());
        $this->assertSame(1, $pleaseSelf->get('case_id'));
        $this->assertSame(1, $pleaseSelf->get('note_id'));


        $problem = $notes[1];
        $this->assertSame("No problem, I'm investigating", $problem->get('body'));

        $problemSelf = $problem->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $problemSelf);
        $this->assertSame('ShowCaseNote', $problemSelf->getName());
        $this->assertSame(1, $problemSelf->get('case_id'));
        $this->assertSame(2, $problemSelf->get('note_id'));
    }
}
