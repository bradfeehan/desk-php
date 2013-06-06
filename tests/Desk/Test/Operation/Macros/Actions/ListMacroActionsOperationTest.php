<?php

namespace Desk\Test\Operation\Macros\Actions;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListMacroActionsOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListMacroActions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'macro_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $actions)
    {
        foreach ($actions as $action) {
            $this->assertSame('MacroActionModel', $action->getStructure()->getName());
        }

        $this->assertSame(2, count($actions));


        $description = $actions[0];
        $this->assertSame('set-case-description', $description->get('type'));
        $this->assertSame('From a VIP Customer', $description->get('value'));
        $this->assertSame(true, $description->get('enabled'));

        $descriptionSelf = $description->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $descriptionSelf);
        $this->assertSame('ShowMacroAction', $descriptionSelf->getName());
        $this->assertSame(1, $descriptionSelf->get('macro_id'));
        $this->assertSame(1, $descriptionSelf->get('action_id'));

        $descriptionMacro = $description->getLink('macro');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $descriptionMacro);
        $this->assertSame('ShowMacro', $descriptionMacro->getName());
        $this->assertSame(1, $descriptionMacro->get('id'));


        $priority = $actions[1];
        $this->assertSame('set-case-priority', $priority->get('type'));
        $this->assertSame('10', $priority->get('value'));
        $this->assertSame(true, $priority->get('enabled'));


        $prioritySelf = $priority->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $prioritySelf);
        $this->assertSame('ShowMacroAction', $prioritySelf->getName());
        $this->assertSame(1, $prioritySelf->get('macro_id'));
        $this->assertSame(2, $prioritySelf->get('action_id'));

        $priorityMacro = $priority->getLink('macro');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $priorityMacro);
        $this->assertSame('ShowMacro', $priorityMacro->getName());
        $this->assertSame(1, $priorityMacro->get('id'));
    }
}
