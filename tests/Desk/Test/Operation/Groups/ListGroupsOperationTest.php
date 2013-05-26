<?php

namespace Desk\Test\Operation\Groups;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListGroupsOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListGroups';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $groups)
    {
        foreach ($groups as $group) {
            $this->assertSame('GroupModel', $group->getStructure()->getName());
        }

        $this->assertSame(2, count($groups));


        $ninjas = $groups[0];
        $this->assertSame('Support Ninjas', $ninjas->get('name'));

        $ninjasSelf = $ninjas->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $ninjasSelf);
        $this->assertSame('ShowGroup', $ninjasSelf->getName());
        $this->assertSame(1, $ninjasSelf->get('id'));


        $admins = $groups[1];
        $this->assertSame('Administrators', $admins->get('name'));

        $adminsSelf = $admins->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $adminsSelf);
        $this->assertSame('ShowGroup', $adminsSelf->getName());
        $this->assertSame(2, $adminsSelf->get('id'));
    }
}
