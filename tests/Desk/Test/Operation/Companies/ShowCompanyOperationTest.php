<?php

namespace Desk\Test\Operation\Companies;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowCompanyOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowCompany';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/companies/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $company)
    {
        $this->assertSame('CompanyModel', $company->getStructure()->getName());

        $this->assertSame('Acme Inc', $company->get('name'));
        $this->assertSame(array('acmeinc.com', 'acmeinc.net'), $company->get('domains'));
        $this->assertSame(array('employer_id' => '123456789'), $company->get('custom_fields'));
        $this->assertInstanceOf('DateTime', $company->get('created_at'));
        $this->assertSame(1369414202, $company->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $company->get('updated_at'));
        $this->assertSame(1369414202, $company->get('updated_at')->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function dataLinksAdditional()
    {
        return array(
            array('customers', 'ListCompanyCustomers', array('company_id' => 1)),
        );
    }
}
