<?php

namespace Desk\Test\Operation\Customers;

use Desk\Relationship\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCustomerOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCustomer';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/customers/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $customer)
    {
        $this->assertSame('CustomerModel', $customer->getStructure()->getName());

        $this->assertSame('John', $customer->get('first_name'));
        $this->assertSame('Doe', $customer->get('last_name'));
        $this->assertSame('ACME, Inc', $customer->get('company'));
        $this->assertSame('Senior Ninja', $customer->get('title'));
        $this->assertInstanceOf('DateTime', $customer->get('created_at'));
        $this->assertSame(1369414202, $customer->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $customer->get('updated_at'));
        $this->assertSame(1369414202, $customer->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('cases', 'ListCustomerCases', array('customer_id' => 1)),
        );
    }
}
