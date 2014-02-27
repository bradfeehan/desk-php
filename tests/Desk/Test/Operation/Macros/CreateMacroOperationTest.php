<?php

namespace Desk\Test\Operation\Macros;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class CreateMacroOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateMacro';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'name' => 'Assign to Engineering',
            'description' => "It's raining fire!",
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array(
            array(
                array('name' => 'Assign to Engineering', 'description' => "It's raining fire!"),
                array('body' => '#^{"name":"Assign to Engineering","description":"It\'s raining fire!"}$#')
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
            array(array('name' => '')),
            array(array('name' => false)),
            array(array('name' => true)),
            array(array('name' => null)),
            array(array('description' => 'foo')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $macro)
    {
        $this->assertSame('MacroModel', $macro->getStructure()->getName());

        $this->assertSame('Assign to Engineering', $macro->get('name'));
        $this->assertSame("It's raining fire!", $macro->get('description'));
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
