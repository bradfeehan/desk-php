<?php

namespace Desk\Test\System;

use Desk\Test\Helper\SystemTestCase;
use Guzzle\Http\Message\RequestFactory;
use Guzzle\Http\QueryAggregator\CommaAggregator;
use Guzzle\Plugin\Oauth\OauthPlugin;

/**
 * @coversNothing
 * @group system
 */
class OauthPluginSystemTest extends SystemTestCase
{
    const TIMESTAMP = '1327274290';
    const NONCE = 'e7aa11195ca58349bec8b5ebe351d3497eb9e603';

    protected $config = array(
        'consumer_key'    => 'foo',
        'consumer_secret' => 'bar',
        'token'           => 'count',
        'token_secret'    => 'dracula'
    );

    protected function getRequest()
    {
        return RequestFactory::getInstance()->create('POST', 'http://www.test.com/path?a=b&c=d', null, array(
            'e' => 'f'
        ));
    }

    public function testMultiDimensionalArrayWithNonDefaultQueryAggregator()
    {
        $p = new OauthPlugin($this->config);
        $request = $this->getRequest();
        $aggregator = new CommaAggregator();
        $query = $request->getQuery()->setAggregator($aggregator)
            ->set('g', array('h', 'i', 'j'))
            ->set('k', array('l'))
            ->set('m', array('n', 'o'));
        $this->assertContains('a%3Db%26c%3Dd%26e%3Df%26g%3Dh%2Ci%2Cj%26k%3Dl%26m%3Dn%2Co', $p->getStringToSign($request, self::TIMESTAMP, self::NONCE));
    }
}
