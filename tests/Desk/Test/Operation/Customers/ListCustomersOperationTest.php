<?php

namespace Desk\Test\Operation\Customers;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCustomersOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCustomers';
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

        $customers = $results->getEmbedded('entries');

        foreach ($customers as $customer) {
            $this->assertInstanceOf('Desk\\Relationship\\Model', $customer);
            $this->assertSame('CustomerModel', $customer->getStructure()->getName());
        }

        $john = $customers[0];

        $this->assertSame('John', $john->get('first_name'));
        $this->assertSame('Doe', $john->get('last_name'));
        $this->assertSame('ACME, Inc', $john->get('company'));
        $this->assertSame('Senior Ninja', $john->get('title'));

        $bob = $customers[1];

        $this->assertSame('Bob', $bob->get('first_name'));
        $this->assertSame('Doe', $bob->get('last_name'));
        $this->assertSame('ACME, Inc', $bob->get('company'));
        $this->assertSame('Senior Ninja', $bob->get('title'));

        // test links to other pages
        $first = $results->getLink('first');

        $this->assertInstanceOf('Guzzle\\Service\\Command\\AbstractCommand', $first);
        $request = $first->prepare();
    }
}
