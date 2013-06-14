<?php

namespace Desk\Test\Unit\Filter;

use DateTime;
use DateTimeZone;
use Desk\Filter\Date;
use Desk\Test\Helper\UnitTestCase;

class DateTest extends UnitTestCase
{

    /**
     * {@inheritdoc}
     */
    public function getMockedClass()
    {
        return 'Desk\\Filter\\Date';
    }

    /**
     * @covers Desk\Filter\Date::stringToObject
     * @dataProvider dataStringToObject
     *
     * @param string  $input    Input date string
     * @param integer $expected Expected Unix timestamp to result
     */
    public function testStringToObject($input, $expected)
    {
        $date = Date::stringToObject($input);
        $this->assertSame($expected, $date->getTimestamp());
    }

    public function dataStringToObject()
    {
        return array(
            array('2013-05-24T16:55:02Z', 1369414502),
            array('2013-12-25T18:02:33Z', 1387994553),
        );
    }

    /**
     * @covers Desk\Filter\Date::objectToString
     * @dataProvider dataObjectToString
     *
     * @param DateTime $date     Input date object
     * @param string   $expected Expected string to result
     */
    public function testObjectToString($date, $expected)
    {
        $originalOffset = $date->getOffset();

        $actual = Date::objectToString($date);
        $this->assertSame($expected, $actual);

        // make sure timezone of the DateTime object is unchanged
        $this->assertSame($originalOffset, $date->getOffset());
    }

    public function dataObjectToString()
    {
        return array(
            array(
                DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2012-06-07 17:00:00',
                    new DateTimeZone('Australia/Melbourne')
                ),
                '2012-06-07T07:00:00Z'
            ),
        );
    }

    /**
     * @covers Desk\Filter\Date::objectToTimestamp
     * @dataProvider dataObjectToTimestamp
     *
     * @param DateTime $date     Input date object
     * @param integer  $expected Expected timestamp to result
     */
    public function testObjectToTimestamp($date, $expected)
    {
        $originalOffset = $date->getOffset();

        $actual = Date::objectToTimestamp($date);
        $this->assertSame($expected, $actual);

        // make sure timezone of the DateTime object is unchanged
        $this->assertSame($originalOffset, $date->getOffset());
    }

    public function dataObjectToTimestamp()
    {
        return array(
            array(
                DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2012-06-07 17:00:00',
                    new DateTimeZone('Australia/Melbourne')
                ),
                1339052400,
            ),
        );
    }
}
