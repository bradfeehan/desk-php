<?php

namespace Desk\Test\System\Client;

use Desk\Client\ServiceDescriptionLoader;
use Desk\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 * @group system
 */
class ServiceDescriptionLoaderSystemTest extends SystemTestCase
{

    public function testMergesDataPropertyCorrectly()
    {
        $loader = new ServiceDescriptionLoader();
        $description = $loader->load(
            array(
                'operations'  => array(
                    'foo' => array(
                        'parameters' => array(
                            'fooParam' => array('type' => 'string'),
                        ),
                        'data' => array(
                            'links' => array(
                                'fooLink' => array('foo' => 'bar'),
                            ),
                        ),
                    ),
                    'bar' => array(
                        'extends' => 'foo',
                        'parameters' => array(
                            'barParam' => array('type' => 'string'),
                        ),
                        'data' => array(
                            'links' => array(
                                'barLink' => array('bar' => 'baz'),
                            ),
                        ),
                    ),
                ),
            )
        );

        $bar = $description->getOperation('bar');
        $links = $bar->getData('links');

        $this->assertArrayHasKey('fooLink', $links);
        $this->assertArrayHasKey('barLink', $links);

        $this->assertSame('bar', $links['fooLink']['foo']);
        $this->assertSame('baz', $links['barLink']['bar']);
    }
}
