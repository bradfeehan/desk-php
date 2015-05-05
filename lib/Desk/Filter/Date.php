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
     * @return \DateTime
     */
    public static function stringToObject($date)
    {
        $zone = new DateTimeZone('UTC');
        return DateTime::createFromFormat(DateTime::ISO8601, $date, $zone);
    }

    /**
     * Converts a DateTime object to a string in the correct format
     *
     * @param \DateTime $date A DateTime object
     *
     * @return string
     */
    public static function objectToString(DateTime $date)
    {
        // clone before setting timezone to UTC to avoid side-effects
        // (since all PHP objects are passed by reference)
        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format("Y-m-d\\TH:i:s\\Z");
    }

    /**
     * Converts a DateTime object to a timestamp
     *
     * @param mixed $date
     * @return int
     */
    public static function toTimestamp($date)
    {
        if ($date instanceof DateTime) {
            return $date->getTimestamp();
        }

        if (!is_numeric($date)) {
            $date = new DateTime($date, new DateTimeZone('UTC'));
            return $date->getTimestamp();
        }

        return $date;
    }
}
