<?php

namespace Desk\Test\System\Command;

use Desk\Command\NullResponseCommand;
use Desk\Test\Helper\SystemTestCase;
use Guzzle\Service\Client;
use Guzzle\Service\Description\Operation;

/**
 * @coversNothing
 * @group system
 */
class NullResponseCommandSystemTest extends SystemTestCase
{

    public function testResponseIsNull()
    {
        $client = new Client('http://mock.localhost');
        $command = new NullResponseCommand(array(), new Operation());
        $this->setMockResponse($client, 'success');

        $result = $client->execute($command);
        $this->assertNull($result);
    }
}
