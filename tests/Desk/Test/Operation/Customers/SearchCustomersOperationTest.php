<?php

namespace Desk\Test\Operation\Customers;

use DateTime;
use DateTimeZone;
use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class SearchCustomersOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'SearchCustomers';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        $date = new DateTime('2013-06-14 13:00:00', new DateTimeZone('UTC'));

        return array(
            array(
                array('first_name' => 'test!'),
                array('query' => '#^first_name=test%21$#'),
            ),
            array(
                array('last_name' => 'abc@def'),
                array('query' => '#^last_name=abc%40def$#'),
            ),
            array(
                array('full_name' => 'fullname'),
                array('query' => '#^full_name=fullname$#'),
            ),
            array(
                array('since_created_at' => $date),
                array('query' => '#^since_created_at=1371214800$#'),
            ),
            array(
                array('max_created_at' => $date),
                array('query' => '#^max_created_at=1371214800$#'),
            ),
            array(
                array('since_updated_at' => $date),
                array('query' => '#^since_updated_at=1371214800$#'),
            ),
            array(
                array('max_updated_at' => $date),
                array('query' => '#^max_updated_at=1371214800$#'),
            ),
            array(
                array('since_id' => 5),
                array('query' => '#^since_id=5$#'),
            ),
            array(
                array('max_id' => 9),
                array('query' => '#^max_id=9$#'),
            ),
            array(
                array('email' => array('foo@example.com', 'bar@example.com')),
                array('query' => '#^email=foo%40example.com,bar%40example.com$#'),
            ),
            array(
                array('twitter' => array('foobar', 'baz')),
                array('query' => '#^twitter=foobar,baz$#'),
            ),
            array(
                array('phone' => array('123 456', '789 012')),
                array('query' => '#^phone=123%20456,789%20012$#'),
            ),
            array(
                array('external_id' => array('abc123', 'xyz789')),
                array('query' => '#^external_id=abc123,xyz789$#'),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalidAdditional()
    {
        return array(
            array(array('first_name' => false)),
            array(array('last_name' => true)),
            array(array('full_name' => -89.2)),
            array(array('since_created_at' => 'abc')),
            array(array('max_created_at' => 123456)),
            array(array('since_updated_at' => false)),
            array(array('max_updated_at' => new \stdClass())),
            array(array('since_id' => '4')),
            array(array('max_id' => 12.8)),
            array(array('email' => array('foo@example.com', false))),
            array(array('twitter' => false)),
            array(array('phone' => array('123 456', true))),
            array(array('external_id' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getSystemTestCommand()
    {
        $command = parent::getSystemTestCommand();
        $command->set(
            'since_created_at',
            new DateTime('2013-06-11 15:43:38', new DateTimeZone('UTC'))
        );
        return $command;
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
