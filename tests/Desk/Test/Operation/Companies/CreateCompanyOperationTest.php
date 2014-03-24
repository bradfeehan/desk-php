<?php

namespace Desk\Test\Operation\Companies;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;
use DateTime;
use DateTimeZone;

/**
 * @coversNothing
 * @group system
 */
class CreateCompanyOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateCompany';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'name' => 'Acme Inc',
            'domains' => array('acmeinc.com', 'acmeinc.net'),
            'custom_fields' => array('employer_id' => '123456789'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        $date = new DateTime('2013-07-11T19:28:25Z', new DateTimeZone('UTC'));

        return array(
            array(array('name' => 'foo'), array('body' => '#^{"name":"foo"}$#')),
            array(
                array('name' => 'foo', 'created_at' => $date),
                array('body' => '#^{"name":"foo","created_at":"2013-07-11T19:28:25Z"}$#'),
            ),
            array(
                array('name' => 'foo', 'domains' => 'example.com'),
                array('body' => '#^{"name":"foo","domains":\\["example.com"\\]}$#'),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterInvalid()
    {
        return array(
            array(array()),
            array(array('name' => null)),
            array(array('name' => false)),
            array(array('name' => new \stdClass())),
            array(array('name' => array('foo'))),
            array(array('name' => 'foo', 'created_at' => 'not a DateTime')),
            array(array('name' => 'foo', 'domains' => false)),
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
        $this->assertSame(1373570905, $company->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $company->get('updated_at'));
        $this->assertSame(1373570905, $company->get('updated_at')->getTimestamp());
        $this->assertSame(array('employer_id' => '123456789'), $company->get('custom_fields'));
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
