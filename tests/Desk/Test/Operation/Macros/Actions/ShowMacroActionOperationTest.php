<?php

namespace Desk\Test\Operation\Macros\Actions;

use Desk\Relationship\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowMacroActionOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowMacroAction';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntegerIdProperties()
    {
        return array('macro_id', 'action_id');
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(
                array('macro_id' => 4, 'action_id' => 6),
                array('url' => '#/macros/4/actions/6$#')
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $action)
    {
        $this->assertSame('MacroActionModel', $action->getStructure()->getName());

        $this->assertSame('set-case-description', $action->get('type'));
        $this->assertSame('From a VIP Customer', $action->get('value'));
        $this->assertSame(true, $action->get('enabled'));
        $this->assertInstanceOf('DateTime', $action->get('created_at'));
        $this->assertSame(1370375051, $action->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $action->get('updated_at'));
        $this->assertSame(1370375051, $action->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('macro', 'ShowMacro', array('id' => 1)),
        );
    }
}
