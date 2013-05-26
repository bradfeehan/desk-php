<?php

namespace Desk\Test\Operation\Users;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListUsersOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListUsers';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $users)
    {
        foreach ($users as $user) {
            $this->assertSame('UserModel', $user->getStructure()->getName());
        }

        $this->assertSame(2, count($users));


        $john = $users[0];
        $this->assertSame('John Doe', $john->get('name'));
        $this->assertSame('John Doe', $john->get('public_name'));
        $this->assertSame('john@acme.com', $john->get('email'));
        $this->assertSame('agent', $john->get('level'));

        $johnSelf = $john->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $johnSelf);
        $this->assertSame('ShowUser', $johnSelf->getName());
        $this->assertSame(1, $johnSelf->get('id'));


        $jane = $users[1];
        $this->assertSame('Jane Doe', $jane->get('name'));
        $this->assertSame('Jane Doe', $jane->get('public_name'));
        $this->assertSame('jane@acme.com', $jane->get('email'));
        $this->assertSame('agent', $jane->get('level'));

        $janeSelf = $jane->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $janeSelf);
        $this->assertSame('ShowUser', $janeSelf->getName());
        $this->assertSame(2, $janeSelf->get('id'));
    }
}
