<?php

namespace LBS\Auth;

use LBS\Auth\Hash;

/**
 * This class handles a number of tasks related to the API.
 * 
 * use LBS\Auth\Api;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.16.1
 */

class Api {

    /**
     * Generate an API key. Single unique place to do this.
     * 
     * @param   string      $id         A unique identifier to encrypt.
     * @param   string|null $random1    A random string to include in the hashing.
     *                                  If null, a random string will be generated. 
     *                                  Default `null`. 
     *                                  In LRS, this should be parsed as `COOKIE_HASH`
     * @param   string|null $random1    A random string to include in the hashing.
     *                                  If null, a random string will be generated. 
     *                                  Default `null`. 
     *                                  In LRS, this should be parsed as `SESSION_HASH`
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.16.1
     * @since   3.28.0  Added param `$random1`, `$random2`
     */

    public static function generate_api_key( string $id, ?string $random1 = null, ?string $random2 = null ): string {
        return md5( 
            $random1 ?? Hash::generate_cookie_hash(
                Hash::random_id_string(),
                Hash::random_id_string(),
                Hash::random_id_string(),
            ) . 
            $random2 ?? Hash::generate_session_hash(
                Hash::random_id_string(),
                Hash::random_id_string(),
            ) . 
            time() . 
            $id
        );
    }


    /**
     * Get the set API key if available. Returns null if none is set
     * 
     * @return  string|null
     * 
     * @access  public
     * @since   3.16.1
     */

    public static function get_key(): ?string {
        $api_key = null;
        if ( isset ( $_GET['auth_key'] ) || isset( $_POST['auth_key'] ) ) {
            $api_key = isset( $_POST['auth_key'] ) ? $_POST['auth_key'] : $_GET['auth_key'];
        }
        return $api_key;
    }

}