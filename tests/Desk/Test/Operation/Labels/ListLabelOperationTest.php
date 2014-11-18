<?php

namespace Desk\Test\Operation\Labels;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListLabelsOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListLabels';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $labels)
    {
        foreach ($labels as $label) {
            $this->assertSame('LabelModel', $label->getStructure()->getName());
        }

        $this->assertSame(2, count($labels));


        $default = $labels[0];
        $this->assertSame('My great label', $default->get('name'));
        $this->assertSame('case', $default->get('types')[0]);
        $this->assertSame('macro', $default->get('types')[1]);
        $this->assertSame('A label to use for great things.', $default->get('description'));
        $this->assertSame(true, $default->get('enabled'));
        $this->assertSame('default', $default->get('color'));

        $defaultSelf = $default->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $defaultSelf);
        $this->assertSame('ShowLabel', $defaultSelf->getName());
        $this->assertSame(1, $defaultSelf->get('id'));

        $pink = $labels[1];
        $this->assertSame('My great label', $pink->get('name'));
        $this->assertSame('case', $pink->get('types')[0]);
        $this->assertSame('macro', $pink->get('types')[1]);
        $this->assertSame('A label to use for great things.', $pink->get('description'));
        $this->assertSame(true, $pink->get('enabled'));
        $this->assertSame('pink', $pink->get('color'));

        $pinkSelf = $pink->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $pinkSelf);
        $this->assertSame('ShowLabel', $pinkSelf->getName());
        $this->assertSame(2, $pinkSelf->get('id'));
    }
}
