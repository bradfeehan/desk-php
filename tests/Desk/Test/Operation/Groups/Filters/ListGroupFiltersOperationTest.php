<?php

namespace Desk\Test\Operation\Groups\Filters;

use Desk\Test\Helper\Operation\ListSubOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListGroupFiltersOperationTest extends ListSubOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListGroupFilters';
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty()
    {
        return 'group_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $filters)
    {
        foreach ($filters as $filter) {
            $this->assertSame('FilterModel', $filter->getStructure()->getName());
        }

        $this->assertSame(2, count($filters));


        $active = $filters[0];
        $this->assertSame('My Active Cases', $active->get('name'));
        $this->assertSame('priority', $active->get('sort'));
        $this->assertSame('desc', $active->get('sort_direction'));
        $this->assertSame(1, $active->get('position'));
        $this->assertSame(true, $active->get('active'));

        $activeGroup = $active->getLink('group');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $activeGroup);
        $this->assertSame('ShowGroup', $activeGroup->getName());
        $this->assertSame(1, $activeGroup->get('id'));


        $spam = $filters[1];
        $this->assertSame('New Cases', $spam->get('name'));
        $this->assertSame('priority', $spam->get('sort'));
        $this->assertSame('desc', $spam->get('sort_direction'));
        $this->assertSame(1, $spam->get('position'));
        $this->assertSame(true, $spam->get('active'));

        $spamGroup = $spam->getLink('group');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $spamGroup);
        $this->assertSame('ShowGroup', $spamGroup->getName());
        $this->assertSame(1, $spamGroup->get('id'));
    }
}
