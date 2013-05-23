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
    public function testSystem()
    {
        $client = $this->client();
        $command = $client->getCommand($this->getOperationName());

        $this->setMockResponse($client, 'success');

        $results = $command->execute();
        $this->assertSame(2, $results->get('total_entries'));

        $users = $results->getEmbedded('entries');

        foreach ($users as $user) {
            $this->assertInstanceOf('Desk\\Relationship\\Model', $user);
            $this->assertSame('UserModel', $user->getStructure()->getName());
        }

        $john = $users[0];

        $this->assertSame('John Doe', $john->get('name'));
        $this->assertSame('John Doe', $john->get('public_name'));
        $this->assertSame('john@acme.com', $john->get('email'));
        $this->assertSame('agent', $john->get('level'));

        $jane = $users[1];

        $this->assertSame('Jane Doe', $jane->get('name'));
        $this->assertSame('Jane Doe', $jane->get('public_name'));
        $this->assertSame('jane@acme.com', $jane->get('email'));
        $this->assertSame('agent', $jane->get('level'));

        // test links to other pages
        $first = $results->getLink('first');

        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $first);
        $request = $first->prepare();
    }
}
