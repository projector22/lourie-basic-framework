<?php

namespace LBF\Auth;

/**
 * This class is used to generate a number of keys and random strings used in the system.
 * 
 * use LBF\Auth\Hash;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.28.0
 * @since   3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class Hash {

    /**
     * Generate a short random id string.
     * 
     * @param   integer     $number_of_chars    The number of characters to return
     *                                          Default: 7
     * @param   string      $input              Insert a string to the element which gets added to the generation process
     *                                          Default: ''
     * 
     * @return  string      Random string of characters
     * 
     * @access  public
     * @since   3.9.0
     * @since   3.28.0  Moved from Functions to `Framework\Auth\Hash`.
     */

    public static function random_id_string( int $number_of_chars = 7, string $input = '' ): string {
        return substr( md5( rand() . $input ), 0, $number_of_chars );
    }


    /**
     * Generate a GUID (v4).
     * 
     * @link https://www.php.net/manual/en/function.com-create-guid.php
     * 
     * @param   boolean $trim   trim {} as required. Default: true.
     * 
     * @return string
     * 
     * @access  public
     * @since   3.21.0
     * @since   3.28.0  Moved from Functions to `Framework\Auth\Hash`.
     */

    public static function generate_guid( bool $trim = true ): string {
        // Windows
        if ( function_exists( 'com_create_guid' ) === true ) {
            if ( $trim === true ) {
                return trim(com_create_guid(), '{}');
            } else {
                return com_create_guid();
            }
        }

        // OSX / Linux
        if ( function_exists( 'openssl_random_pseudo_bytes' ) === true ) {
            $data = openssl_random_pseudo_bytes( 16 );
            $data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 );    // set version to 0100
            $data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 );    // set bits 6-7 to 10
            return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
        }

        // Fallback (PHP 4.2+)
        mt_srand( (double)microtime() * 10000 );
        $charid = strtolower( md5( uniqid( rand(), true ) ) );
        $hyphen = chr( 45 );                  // "-"
        $lbrace = $trim ? "" : chr( 123 );    // "{"
        $rbrace = $trim ? "" : chr( 125 );    // "}"
        return $lbrace .
                substr( $charid,  0,  8 ) . $hyphen .
                substr( $charid,  8,  4 ) . $hyphen .
                substr( $charid, 12,  4 ) . $hyphen .
                substr( $charid, 16,  4 ) . $hyphen .
                substr( $charid, 20, 12 ) . 
                $rbrace;
    }


    /**
     * Generate a cookie hash used by the system.
     * 
     * @param   string  $part1  A random string to be a part of the hash. In LRS this should be parsed as `TBL_PFX`.
     * @param   string  $part2  A random string to be a part of the hash. In LRS this should be parsed as `DB_NAME`.
     * @param   string  $part3  A random string to be a part of the hash. In LRS this should be parsed as `SCHOOL_NAME`.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.28.0
     */

    public static function generate_cookie_hash( string $part1, string $part2, string $part3 ): string {
        return md5( 
            date( 'Y-m-d G:i:s' ) . 
            time() . 
            $part1 . 
            $part2 . 
            $part3 
        );
    }


    /**
     * Generate a cookie hash used by the system.
     * 
     * @param   string  $part1  A random string to be a part of the hash. In LRS this should be parsed as `TBL_PFX`.
     * @param   string  $part2  A random string to be a part of the hash. In LRS this should be parsed as `DB_NAME`.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.28.0
     */

    public static function generate_session_hash( string $part1, string $part2 ): string {
        return md5( 
            date( 'Y-m-d G:i:s' ) . 
            time() . 
            $part1 . 
            $part2 
        );
    }

}