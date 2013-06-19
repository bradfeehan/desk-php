<?php

namespace Desk\Test\Operation\CustomFields;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCustomFieldOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCustomField';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/custom_fields/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $field)
    {
        $this->assertSame('CustomFieldModel', $field->getStructure()->getName());

        $this->assertSame('frequent_buyer', $field->get('name'));
        $this->assertSame('Frequent Buyer', $field->get('label'));
        $this->assertSame('customer', $field->get('type'));
        $this->assertSame(true, $field->get('active'));
    }
}
