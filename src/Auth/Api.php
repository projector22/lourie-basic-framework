<?php

namespace Framework\Auth;

/**
 * This class handles a number of tasks related to the API.
 * 
 * use Framework\Auth\Api;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.16.1
 */

class Api {

    /**
     * Generate an API key. Single unique place to do this
     * 
     * @param   string  $id     A unique identifier to encrypt.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.16.1
     */

    public static function generate_api_key( string $id ): string {
        return md5( COOKIE_HASH . SESSION_HASH . time() . $id );
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