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
    protected function assertSystem(array $customers)
    {
        foreach ($customers as $customer) {
            $this->assertSame('CustomerModel', $customer->getStructure()->getName());
        }

        $this->assertSame(2, count($customers));


        $john = $customers[0];
        $this->assertSame('John', $john->get('first_name'));
        $this->assertSame('Doe', $john->get('last_name'));
        $this->assertSame('ACME, Inc', $john->get('company'));
        $this->assertSame('Senior Ninja', $john->get('title'));

        $johnSelf = $john->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $johnSelf);
        $this->assertSame('ShowCustomer', $johnSelf->getName());
        $this->assertSame(1, $johnSelf->get('id'));

        $johnCases = $john->getLink('cases');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $johnCases);
        $this->assertSame('ListCustomerCases', $johnCases->getName());
        $this->assertSame(1, $johnCases->get('customer_id'));


        $bob = $customers[1];
        $this->assertSame('Bob', $bob->get('first_name'));
        $this->assertSame('Doe', $bob->get('last_name'));
        $this->assertSame('ACME, Inc', $bob->get('company'));
        $this->assertSame('Senior Ninja', $bob->get('title'));

        $bobSelf = $bob->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $bobSelf);
        $this->assertSame('ShowCustomer', $bobSelf->getName());
        $this->assertSame(2, $bobSelf->get('id'));

        $bobCases = $bob->getLink('cases');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $bobCases);
        $this->assertSame('ListCustomerCases', $bobCases->getName());
        $this->assertSame(2, $bobCases->get('customer_id'));
    }
}
