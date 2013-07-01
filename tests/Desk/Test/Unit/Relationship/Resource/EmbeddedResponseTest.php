<?php

namespace Desk\Test\Unit\Relationship\Resource;

use Desk\Test\Helper\UnitTestCase;

class EmbeddedResponseTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Resource\\EmbeddedResponse';
    }

    /**
     * @covers Desk\Relationship\Resource\EmbeddedResponse::setReasonPhrase
     */
    public function testSetReasonPhrase()
    {
        $response = $this->mock('setReasonPhrase');
        $response->setReasonPhrase('the reason');

        $reason = $this->getPrivateProperty($response, 'reasonPhrase');
        $this->assertSame('the reason', $reason);
    }
}
