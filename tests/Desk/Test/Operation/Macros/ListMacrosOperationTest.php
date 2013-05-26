<?php

namespace Desk\Test\Operation\Macros;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListMacrosOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListMacros';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $macros)
    {
        foreach ($macros as $macro) {
            $this->assertSame('MacroModel', $macro->getStructure()->getName());
        }

        $this->assertSame(2, count($macros));


        $repeat = $macros[0];
        $this->assertSame('Macro Macro', $repeat->get('name'));
        $this->assertSame('On repeat', $repeat->get('description'));
        $this->assertSame(true, $repeat->get('enabled'));
        $this->assertSame(1, $repeat->get('position'));
        $this->assertSame(array('Sample Macros', 'Favorites'), $repeat->get('folders'));

        $repeatSelf = $repeat->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $repeatSelf);
        $this->assertSame('ShowMacro', $repeatSelf->getName());
        $this->assertSame(1, $repeatSelf->get('id'));

        $repeatActions = $repeat->getLink('actions');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $repeatActions);
        $this->assertSame('ListMacroActions', $repeatActions->getName());
        $this->assertSame(1, $repeatActions->get('macro_id'));


        $another = $macros[1];
        $this->assertSame('Another Macro', $another->get('name'));
        $this->assertSame(null, $another->get('description'));
        $this->assertSame(true, $another->get('enabled'));
        $this->assertSame(2, $another->get('position'));
        $this->assertSame(array('Sample Macros', 'Favorites'), $another->get('folders'));

        $anotherSelf = $another->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $anotherSelf);
        $this->assertSame('ShowMacro', $anotherSelf->getName());
        $this->assertSame(2, $anotherSelf->get('id'));

        $anotherActions = $another->getLink('actions');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $anotherActions);
        $this->assertSame('ListMacroActions', $anotherActions->getName());
        $this->assertSame(2, $anotherActions->get('macro_id'));
    }
}
