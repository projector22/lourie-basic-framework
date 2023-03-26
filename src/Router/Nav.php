<?php

namespace LBF\Router;

/**
 * Class for navigating or loading page contents.
 * 
 * use LBF\Router\Nav;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

class Nav {

    /**
     * Redirect the page to the defined location.
     * 
     * @param   string  $location   The location to navigate to.
     * 
     * @return  never
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function redirect( string $location ): never {
        header( "Location: {$location}" );
        die;
    }

}