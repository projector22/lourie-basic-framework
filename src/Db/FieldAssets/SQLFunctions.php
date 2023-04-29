<?php

namespace LBF\Db\FieldAssets;

/**
 * Enum of various usable SQL functions for default values on an SQL field.
 * 
 * use LBF\Db\FieldAssets\SQLFunctions;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @see https://dev.mysql.com/doc/refman/8.0/en/date-and-time-functions.html
 * 
 * @since   LBF 0.7.0-beta
 */

enum SQLFunctions {
    /**
     * Return the current date and time.
     * 
     * `NOW()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case NOW;
    /**
     * Synonym for DAYOFMONTH().
     * 
     * `DATE()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case DATE;
    /**
     * Return the year.
     * 
     * `YEAR()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case YEAR;
    /**
     * Return the month from the date passed.
     * 
     * `YEAR()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case MONTH;
    /**
     * Synonym for DAYOFMONTH().
     * 
     * `MONTH()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case DAY;
    /**
     * Return a random floating-point value.
     * 
     * `RAND()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case RAND;
    /**
     * Return the current date.
     * 
     * `CURDATE()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case CURDATE;
    /**
     * Return the current time.
     * 
     * `CURTIME()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case CURTIME;
    /**
     * Synonyms for CURDATE().
     * 
     * `CURRENT_DATE`
     * 
     * @since   LBF 0.7.0-beta
     */
    case CURRENT_DATE;
    /**
     * Synonyms for CURTIME().
     * 
     * `CURRENT_TIME`
     * 
     * @since   LBF 0.7.0-beta
     */
    case CURRENT_TIME;
    /**
     * Synonyms for NOW().
     * 
     * `CURRENT_TIMESTAMP`
     * 
     * @since   LBF 0.7.0-beta
     */
    case CURRENT_TIMESTAMP;
    /**
     * With a single argument, this function returns the date or datetime expression; with two arguments, the sum of the arguments.
     * 
     * `TIMESTAMP(x)`
     * 
     * @since   LBF 0.7.0-beta
     */
    case TIMESTAMP;
    /**
     * Return a Unix timestamp.
     * 
     * `UNIX_TIMESTAMP`
     * 
     * @since   LBF 0.7.0-beta
     */
    case UNIX_TIMESTAMP;
    /**
     * Return a Universal Unique Identifier (UUID)
     * 
     * `UUID()`
     * 
     * @since   LBF 0.7.0-beta
     */
    case UUID;


    /**
     * Return the actual function string of the requested ENUM.
     * 
     * @param   string  $value  Default: ''. The param to pass to the function
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

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
