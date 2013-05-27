<?php

namespace Desk\Filter;

use DateTime;
use DateTimeZone;

/**
 * Converts dates between DateTime objects and ISO 8601 format strings
 */
class Date
{

    /**
     * Converts a date string (in the correct format) to a date object
     *
     * @param string $date A date string from the Desk.com API
     *
     * @return DateTime
     */
    public static function stringToObject($date)
    {
        $format = DateTime::ISO8601;
        return DateTime::createFromFormat(DateTime::ISO8601, $date);
    }

    /**
     * Converts a DateTime object to a string in the correct format
     *
     * @param DateTime $date A DateTime object
     *
     * @return string
     */
    public static function objectToString(DateTime $date)
    {
        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format("Y-m-d\\TH:i:s\\Z");
    }
}
