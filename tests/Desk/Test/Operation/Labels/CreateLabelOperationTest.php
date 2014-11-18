<?php

namespace Desk\Test\Operation\Labels;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class CreateLabelOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getOperationName()
    {
        return 'CreateLabel';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'name' => 'My great label',
            'description' => 'A label to use for great things.',
            'enabled' => true,
            'types' => array('case', 'macro'),
            'color' => 'default'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $body = '{"name":"foo","types":\["case","macro"\]}';

        return array(
            array(
                array('name' => 'foo', 'types' => array('case', 'macro')),
                array('body' => "/^$body$/"),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array()),
            array(array('name' => null, 'type' => array('case'))),
            array(array('name' => true, 'type' => array('case'))),
            array(array('name' => false, 'type' => array('case'))),
            array(array('name' => new \stdClass(), 'type' => array('case')))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assertSystem(Model $label)
    {
        $this->assertSame('LabelModel', $label->getStructure()->getName());

        $this->assertSame('My great label', $label->get('name'));
        $this->assertSame(2, count($label->get('types')));
        $this->assertSame('case', $label->get('types')[0]);
        $this->assertSame('macro', $label->get('types')[1]);
        $this->assertSame('A label to use for great things.', $label->get('description'));
        $this->assertSame(true, $label->get('enabled'));
        $this->assertSame('default', $label->get('color'));
    }
}
