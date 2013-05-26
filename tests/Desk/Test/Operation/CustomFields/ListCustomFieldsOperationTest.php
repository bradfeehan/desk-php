<?php

namespace Desk\Test\Operation\CustomFields;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCustomFieldsOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCustomFields';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $fields)
    {
        foreach ($fields as $field) {
            $this->assertSame('CustomFieldModel', $field->getStructure()->getName());
        }

        $this->assertSame(2, count($fields));


        $buyer = $fields[0];
        $this->assertSame('frequent_buyer', $buyer->get('name'));
        $this->assertSame('Frequent Buyer', $buyer->get('label'));
        $this->assertSame('customer', $buyer->get('type'));
        $this->assertSame(true, $buyer->get('active'));

        $buyerSelf = $buyer->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $buyerSelf);
        $this->assertSame('ShowCustomField', $buyerSelf->getName());
        $this->assertSame(1, $buyerSelf->get('id'));


        $agent = $fields[1];
        $this->assertSame('last_agent', $agent->get('name'));
        $this->assertSame('Last Agent to Update', $agent->get('label'));
        $this->assertSame('ticket', $agent->get('type'));
        $this->assertSame(false, $agent->get('active'));

        $agentSelf = $agent->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $agentSelf);
        $this->assertSame('ShowCustomField', $agentSelf->getName());
        $this->assertSame(2, $agentSelf->get('id'));
    }
}
