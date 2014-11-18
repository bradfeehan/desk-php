<?php

namespace Desk\Test\Operation\Labels;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowLabelOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowLabel';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/labels/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $label)
    {
        $this->assertSame('LabelModel', $label->getStructure()->getName());

        $this->assertSame('My great label', $label->get('name'));
        $this->assertSame('case', $label->get('types')[0]);
        $this->assertSame('macro', $label->get('types')[1]);
        $this->assertSame('A label to use for great things.', $label->get('description'));
        $this->assertSame(true, $label->get('enabled'));
        $this->assertSame('default', $label->get('color'));
    }
}
