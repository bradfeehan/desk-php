<?php

namespace Desk\Test\Operation\Customers;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class CreateCustomerOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateCustomer';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'emails' => array(
                array('type' => 'work', 'value' => 'john@acme.com'),
                array('type' => 'home', 'value' => 'john@home.com'),
            ),
            'custom_fields' => array('level' => 'vip'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array(
            array(array(), array('body' => '#^$#')),
            array(
                array('first_name' => 'Test'),
                array('body' => '#^{"first_name":"Test"}$#'),
            ),
            array(
                array('company_id' => 4),
                array(
                    'body' => '#^{"_links":{"company":{"class":"company",' .
                        '"href":"\\\\/api\\\\/v2\\\\/companies\\\\/4"}}}$#'
                ),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array('first_name' => false)),
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
        $this->assertNull($customer->get('company'));
        $this->assertNull($customer->get('title'));
        $this->assertNull($customer->get('external_id'));
        $this->assertNull($customer->get('background'));
        $this->assertNull($customer->get('locked_until'));
        $this->assertInstanceOf('DateTime', $customer->get('created_at'));
        $this->assertSame(1373570905, $customer->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $customer->get('updated_at'));
        $this->assertSame(1373570905, $customer->get('updated_at')->getTimestamp());
        $this->assertSame(array('level' => 'vip'), $customer->get('custom_fields'));

        $emails = array(
            array('type' => 'work', 'value' => 'john@acme.com'),
            array('type' => 'home', 'value' => 'john@home.com'),
        );
        $this->assertSame($emails, $customer->get('emails'));
        $this->assertSame(array(), $customer->get('phone_numbers'));
        $this->assertSame(array(), $customer->get('addresses'));
        $this->assertNull($customer->get('locked_by'));
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
