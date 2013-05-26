<?php

namespace Desk\Test\Operation\Companies;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListCompaniesOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListCompanies';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $companies)
    {
        foreach ($companies as $company) {
            $this->assertSame('CompanyModel', $company->getStructure()->getName());
        }

        $this->assertSame(2, count($companies));


        $acme = $companies[0];
        $this->assertSame('Acme Inc', $acme->get('name'));
        $this->assertSame(array('acmeinc.com', 'acmeinc.net'), $acme->get('domains'));
        $this->assertSame(array('employer_id' => '123456789'), $acme->get('custom_fields'));

        $acmeSelf = $acme->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $acmeSelf);
        $this->assertSame('ShowCompany', $acmeSelf->getName());
        $this->assertSame(1, $acmeSelf->get('id'));

        $acmeCustomers = $acme->getLink('customers');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $acmeCustomers);
        $this->assertSame('ListCompanyCustomers', $acmeCustomers->getName());
        $this->assertSame(1, $acmeCustomers->get('company_id'));


        $desk = $companies[1];
        $this->assertSame('Desk.com', $desk->get('name'));
        $this->assertSame(array('desk.com', 'salesforce.com'), $desk->get('domains'));
        $this->assertSame(array('employer_id' => '123456789'), $desk->get('custom_fields'));

        $deskSelf = $desk->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $deskSelf);
        $this->assertSame('ShowCompany', $deskSelf->getName());
        $this->assertSame(2, $deskSelf->get('id'));

        $deskCustomers = $desk->getLink('customers');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $deskCustomers);
        $this->assertSame('ListCompanyCustomers', $deskCustomers->getName());
        $this->assertSame(2, $deskCustomers->get('company_id'));
    }
}
