<?php

namespace Desk\Test\Operation\Filters;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListFiltersOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListFilters';
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
        $this->assertSame('Spam Cases', $spam->get('name'));
        $this->assertSame('priority', $spam->get('sort'));
        $this->assertSame('desc', $spam->get('sort_direction'));
        $this->assertSame(2, $spam->get('position'));
        $this->assertSame(true, $spam->get('active'));

        $spamUser = $spam->getLink('user');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $spamUser);
        $this->assertSame('ShowUser', $spamUser->getName());
        $this->assertSame(1, $spamUser->get('id'));
    }
}
