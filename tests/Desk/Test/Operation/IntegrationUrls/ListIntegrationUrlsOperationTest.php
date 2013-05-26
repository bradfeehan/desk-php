<?php

namespace Desk\Test\Operation\IntegrationUrls;

use Desk\Test\Helper\Operation\ListOperationTestCase;

/**
 * @coversNothing
 * @group system
 */
class ListIntegrationUrlsOperationTest extends ListOperationTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getOperationName()
    {
        return 'ListIntegrationUrls';
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSystem(array $urls)
    {
        foreach ($urls as $url) {
            $this->assertSame('IntegrationUrlModel', $url->getStructure()->getName());
        }

        $this->assertSame(2, count($urls));


        $sample = $urls[0];
        $this->assertSame('Sample URL', $sample->get('name'));
        $this->assertSame('A sample Integration URL', $sample->get('description'));
        $this->assertSame(true, $sample->get('enabled'));
        $this->assertSame('http://www.example.com/name={{customer.name | url_encode}}', $sample->get('markup'));
        $this->assertSame('http://www.example.com/name=', $sample->get('rendered'));

        $sampleSelf = $sample->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $sampleSelf);
        $this->assertSame('ShowIntegrationUrl', $sampleSelf->getName());
        $this->assertSame(1, $sampleSelf->get('id'));


        $another = $urls[1];
        $this->assertSame('Another URL', $another->get('name'));
        $this->assertSame('A sample Integration URL', $another->get('description'));
        $this->assertSame(true, $another->get('enabled'));
        $this->assertSame('http://www.example.com/caseid={{case.id}}', $another->get('markup'));
        $this->assertSame('http://www.example.com/caseid=', $another->get('rendered'));

        $anotherSelf = $another->getLink('self');
        $this->assertInstanceOf('Guzzle\\Service\\Command\\OperationCommand', $anotherSelf);
        $this->assertSame('ShowIntegrationUrl', $anotherSelf->getName());
        $this->assertSame(2, $anotherSelf->get('id'));

    }
}
