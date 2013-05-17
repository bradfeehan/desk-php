<?php

namespace Desk\Test\Unit\Relationship\Exception;

use Desk\Relationship\Exception\InvalidEmbedFormatException;
use Desk\Test\Helper\UnitTestCase;

class InvalidEmbedFormatExceptionTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Exception\\InvalidEmbedFormatException';
    }

    /**
     * @covers Desk\Relationship\Exception\InvalidEmbedFormatException::fromEmbed
     * @dataProvider dataFromEmbed
     *
     * @param array  $embed           The embedded resource
     * @param string $expectedMessage The expected resulting message
     */
    public function testFromLink($embed, $expectedMessage = null)
    {
        $exception = InvalidEmbedFormatException::fromEmbed($embed);

        $this->assertInstanceOf($this->getMockedClass(), $exception);

        if ($expectedMessage) {
            $this->assertSame($exception->getMessage(), $expectedMessage);
        }
    }

    public function dataFromEmbed()
    {
        return array(
            array(
                array(),
                "Invalid embedded resource format: missing expected " .
                "'_links' element; missing expected 'self' link",
            ),
            array(
                array('_links' => array('foo' => 'bar')),
                "Invalid embedded resource format: missing expected " .
                "'self' link",
            ),
        );
    }
}
