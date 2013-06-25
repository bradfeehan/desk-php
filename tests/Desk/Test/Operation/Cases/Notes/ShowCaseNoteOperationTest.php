<?php

namespace Desk\Test\Operation\Cases\Notes;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCaseNoteOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCaseNote';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('case_id', 'note_id');
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('case_id' => 4, 'note_id' => 3),
                array('url' => '#/cases/4/notes/3$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $note)
    {
        $this->assertSame('CaseNoteModel', $note->getStructure()->getName());

        $this->assertSame('Please assist me with this case', $note->get('body'));
        $this->assertInstanceOf('DateTime', $note->get('created_at'));
        $this->assertSame(1370375051, $note->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $note->get('updated_at'));
        $this->assertSame(1370375051, $note->get('updated_at')->getTimestamp());
        $this->assertSame(null, $note->get('erased_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('case', 'ShowCase', array('id' => 1)),
            array('user', 'ShowUser', array('id' => 1)),
        );
    }
}
