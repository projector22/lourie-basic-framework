<?php

namespace LBF\Auth;

use LBF\Auth\Hash;

/**
 * This class handles a number of tasks related to the API.
 * 
 * use LBF\Auth\Api;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.16.1
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class Api {

    /**
     * Generate an API key. Single unique place to do this.
     * 
     * @param   string  $id         A unique identifier to encrypt.
     * @param   string  ...$random  A random string or strings to include in the hashing process.
     *                              If blank, a series of random strings will be parsed.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LRS 3.16.1
     * @since   LRS 3.28.0      Added param `$random1`, `$random2`.
     * @since   LBF 0.5.2-beta  Changed how params are parsed so multiple may be parsed.
     */

    public static function generate_api_key( string $id, string ...$random ): string {
        $random = count( $random ) > 0 ? implode( '', $random ) : null;
        return md5( 
            $random ?? ( Hash::generate_cookie_hash(
                Hash::random_id_string(),
                Hash::random_id_string(),
                Hash::random_id_string(),
            ) . Hash::generate_session_hash(
                Hash::random_id_string(),
                Hash::random_id_string(),
            ) ) . time() . $id
        );
    }


    /**
     * Get the set API key if available. Returns null if none is set
     * 
     * @return  string|null
     * 
     * @access  public
     * @since   LRS 3.16.1
     */

    public static function get_key(): ?string {
        $api_key = null;
        if ( isset ( $_GET['auth_key'] ) || isset( $_POST['auth_key'] ) ) {
            $api_key = isset( $_POST['auth_key'] ) ? $_POST['auth_key'] : $_GET['auth_key'];
        }
        return $api_key;
    }

}