<?php

namespace Desk\Test\Operation\IntegrationUrls;

use Desk\Relationship\Model;
use Desk\Test\Helper\Operation\ShowOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ShowIntegrationUrlOperationTest extends ShowOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ShowIntegrationUrl';
    }

    /**
     * {@inheritdoc}
     */
    public function dataParameterValidAdditional()
    {
        return array(
            array(array('id' => 4), array('url' => '#/integration_urls/4$#')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(Model $url)
    {
        $this->assertSame('IntegrationUrlModel', $url->getStructure()->getName());

        $this->assertSame('Sample URL', $url->get('name'));
        $this->assertSame('A sample Integration URL', $url->get('description'));
        $this->assertSame(true, $url->get('enabled'));
        $this->assertSame('http://www.example.com/name={{customer.name | url_encode}}', $url->get('markup'));
        $this->assertSame('http://www.example.com/name=Andrew', $url->get('rendered'));
        $this->assertInstanceOf('DateTime', $url->get('created_at'));
        $this->assertSame(1335994728, $url->get('created_at')->getTimestamp());
        $this->assertInstanceOf('DateTime', $url->get('updated_at'));
        $this->assertSame(1335994728, $url->get('updated_at')->getTimestamp());
    }
}
