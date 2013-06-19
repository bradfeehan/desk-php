<?php

namespace Desk\Test\Operation\Macros;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowMacroOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowMacro';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/macros/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $macro)
    {
        $this->assertSame('MacroModel', $macro->getStructure()->getName());

        $this->assertSame('Macro Macro', $macro->get('name'));
        $this->assertSame('On repeat', $macro->get('description'));
        $this->assertSame(true, $macro->get('enabled'));
        $this->assertSame(1, $macro->get('position'));
        $this->assertSame(array('Sample Macros', 'Favorites'), $macro->get('folders'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('actions', 'ListMacroActions', array('macro_id' => 1)),
        );
    }
}
