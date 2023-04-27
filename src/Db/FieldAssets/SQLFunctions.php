<?php

namespace LBF\Db\FieldAssets;

/**
 * @see https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html
 */

enum SQLFunctions {
    /**
     * Return the current date and time
     */
    case NOW;
    /**
     * Synonym for DAYOFMONTH()
     */
    case DATE;
    /**
     * Return the year
     */
    case YEAR;
    /**
     * Return the month from the date passed
     */
    case MONTH;
    /**
     * Synonym for DAYOFMONTH()
     */
    case DAY;
    /**
     * Return a random floating-point value
     */
    case RAND;
    /**
     * Return the current date
     */
    case CURDATE;
    /**
     * Return the current time
     */
    case CURTIME;
    /**
     * Synonyms for CURDATE()
     */
    case CURRENT_DATE;
    /**
     * Synonyms for CURTIME()
     */
    case CURRENT_TIME;
    /**
     * Synonyms for NOW()
     */
    case CURRENT_TIMESTAMP;
    /**
     * With a single argument, this function returns the date or datetime expression; with two arguments, the sum of the arguments
     */
    case TIMESTAMP;
    /**
     * Return a Unix timestamp
     */
    case UNIX_TIMESTAMP;
    /**
     * Return a Universal Unique Identifier (UUID)
     */
    case UUID;

    public function fn(string $value = ''): string {
        return match ($this) {
            self::NOW               => "NOW({$value})",
            self::DATE              => "DATE({$value})",
            self::YEAR              => "YEAR({$value})",
            self::MONTH             => "MONTH({$value})",
            self::DAY               => "DAY({$value})",
            self::RAND              => "RAND({$value})",
            self::CURDATE           => 'CURDATE()',
            self::CURTIME           => 'CURTIME()',
            self::CURRENT_TIMESTAMP => "CURRENT_TIMESTAMP",
            self::CURRENT_DATE      => "CURRENT_DATE",
            self::CURRENT_TIME      => "CURRENT_TIME",
            self::TIMESTAMP         => "TIMESTAMP({$value})",
            self::UNIX_TIMESTAMP    => 'UNIX_TIMESTAMP()',
            self::UUID              => 'UUID()',
        };
    }
}
