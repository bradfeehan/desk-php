<?php

namespace Desk\Test\Operation\Customers;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\UpdateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class UpdateCustomerOperationTest extends UpdateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'UpdateCustomer';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'id' => 1,
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
            array(array('id' => 1), array('body' => '#^$#')),
            array(
                array('id' => 1, 'first_name' => 'Test'),
                array('body' => '#^{"first_name":"Test"}$#'),
            ),
            array(
                array('id' => 1, 'company_id' => 4),
                array(
                    'body' => '#^{"_links":{"company":{"class":"company",' .
                        '"href":"\\\\/api\\\\/v2\\\\/companies\\\\/4"}}}$#'
                ),
            ),
            array(
                array('id' => 1, 'addresses_update_action' => 'replace'),
                array('body' => '#^{"addresses_update_action":"replace"}$#'),
            ),
            array(
                array('id' => 1, 'emails_update_action' => 'append'),
                array('body' => '#^{"emails_update_action":"append"}$#'),
            ),
            array(
                array('id' => 1, 'phone_numbers_update_action' => 'replace'),
                array('body' => '#^{"phone_numbers_update_action":"replace"}$#'),
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

        $this->assertSame(1, $customer->get('id'));
        $this->assertSame('John', $customer->get('first_name'));
        $this->assertSame('Doe', $customer->get('last_name'));
        $this->assertNull($customer->get('company'));
        $this->assertSame('', $customer->get('title'));
        $this->assertNull($customer->get('external_id'));
        $this->assertSame('', $customer->get('background'));
        $this->assertNull($customer->get('locked_until'));
        $this->assertInstanceOf('DateTime', $customer->get('created_at'));
        $this->assertSame(1354558438, $customer->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $customer->get('updated_at'));
        $this->assertSame(1412793206, $customer->get('updated_at')->getTimestamp());
        $this->assertSame(array('level' => 'vip'), $customer->get('custom_fields'));

        $emails = array(
            array('type' => 'work', 'value' => 'john@acme.com'),
            array('type' => 'home', 'value' => 'john@home.com'),
        );
        $this->assertSame($emails, $customer->get('emails'));
        $this->assertSame(array(), $customer->get('phone_numbers'));
        $this->assertSame(array(), $customer->get('addresses'));
        $this->assertNull($customer->get('locked_by'));
        $this->assertSame('http://www.gravatar.com/avatar/1', $customer->get('avatar'));
        $this->assertFalse($customer->get('access_company_cases'));
        $this->assertTrue($customer->get('access_private_portal'));
        $this->assertTrue($customer->get('access_private_portal'));
        $this->assertNull($customer->get('language'));
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
