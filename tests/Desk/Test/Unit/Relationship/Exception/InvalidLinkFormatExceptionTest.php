<?php

namespace Desk\Test\Unit\Relationship\Exception;

use Desk\Relationship\Exception\InvalidLinkFormatException;
use Desk\Test\Helper\UnitTestCase;

class InvalidLinkFormatExceptionTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Relationship\\Exception\\InvalidLinkFormatException';
    }

    /**
     * @covers Desk\Relationship\Exception\InvalidLinkFormatException::fromLink
     * @dataProvider dataFromLink
     *
     * @param array  $link            The link to pass to fromLink()
     * @param string $expectedMessage The expected resulting message
     */
    public function testFromLink($link, $expectedMessage = null)
    {
        $exception = InvalidLinkFormatException::fromLink($link);

        $this->assertInstanceOf($this->getMockedClass(), $exception);

        if ($expectedMessage) {
            $this->assertSame($exception->getMessage(), $expectedMessage);
        }
    }

    public function dataFromLink()
    {
        return array(
            array(
                array(),
                "Invalid resource link format: missing expected " .
                "'class' element; missing expected 'href' element",
            ),
            array(
                array('href' => 'foo'),
                "Invalid resource link format: missing expected " .
                "'class' element",
            ),
            array(
                array('class' => 'foo'),
                "Invalid resource link format: missing expected " .
                "'href' element",
            ),
        );
    }
}
