<?php

namespace Desk\Test\Operation\IntegrationUrls;

use Desk\Relationship\Resource\Model;
use Desk\Test\Helper\Operation\CreateOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class CreateIntegrationUrlOperationTest extends CreateOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'CreateIntegrationUrl';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSystemParameters()
    {
        return array(
            'name' => 'Sample URL',
            'description' => 'A sample Integration URL',
            'markup' => 'http://www.example.com',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValid()
    {
        return array(
            array(
                array('name' => 'foo', 'markup' => 'bar'),
                array('body' => '#^{"name":"foo","markup":"bar"}$#')
            ),
            array(
                array('name' => 'foo', 'description' => 'bar', 'markup' => 'baz'),
                array('body' => '#^{"name":"foo","description":"bar","markup":"baz"}$#')
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
            array(array('name' => 'a')),
            array(array('name' => 'foo', 'markup' => false)),
            array(array('markup' => 'foo')),
            array(array('name' => '', 'markup' => 'no name')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $integrationUrl)
    {
        $this->assertSame('IntegrationUrlModel', $integrationUrl->getStructure()->getName());

        $this->assertSame('Sample URL', $integrationUrl->get('name'));
        $this->assertSame('A sample Integration URL', $integrationUrl->get('description'));
        $this->assertSame(false, $integrationUrl->get('enabled'));
        $this->assertSame('http://www.example.com/name={{customer.name | url_encode}}', $integrationUrl->get('markup'));
        $this->assertSame('http://www.example.com/name=', $integrationUrl->get('rendered'));
        $this->assertInstanceOf('DateTime', $integrationUrl->get('created_at'));
        $this->assertSame(1374867467, $integrationUrl->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $integrationUrl->get('updated_at'));
        $this->assertSame(1374867467, $integrationUrl->get('updated_at')->getTimestamp());
    }
}
