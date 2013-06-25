<?php

namespace Desk\Test\Operation\Filters;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowFilterOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowFilter';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/filters/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $filter)
    {
        $this->assertSame('FilterModel', $filter->getStructure()->getName());

        $this->assertSame('My Active Cases', $filter->get('name'));
        $this->assertSame('priority', $filter->get('sort'));
        $this->assertSame('desc', $filter->get('sort_direction'));
        $this->assertSame(1, $filter->get('position'));
        $this->assertSame(true, $filter->get('active'));
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('group', 'ShowGroup', array('id' => 1)),
            array('user', 'ShowUser', array('id' => 1)),
        );
    }
}
