<?php

namespace LBF\App;

use AllowDynamicProperties;
use stdClass;

/**
 * Class for holding the payload of config files to be used by the app.
 * Load in a config array and it should be available via `Config::$payload->my_key` throughout the app.
 * 
 * use LBF\App\Config;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

#[AllowDynamicProperties]
class Config {


    /**
     * The payload used throughout the app.
     * 
     * @var stdClass    $payload
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static stdClass $payload;

    /**
     * The object containing the user account object;
     * 
     * @var object  $user;
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static object $user;


    /**
     * Load any config data into the `Config::$payload` object.
     * 
     * @param   array   $config     The config to load.
     * @param   bool    $overwrite  Whether or not to overwrite the previous config.
     *                              Default: false.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function load( array $config, bool $overwrite = false ): void {
        if ( !isset( self::$payload ) || $overwrite == true ) {
            self::$payload = new stdClass;
            self::$payload->meta = [
                'program_name'    => 'YOUR PROGRAM NAME',
                'description'     => 'A basic PHP Framework',
                'project_version' => '0.1.0',
                'project_status'  => '',
                'page_title'      => 'Lourie Basic Framework',
                'favicon'         => '',
                'site_language'   => 'en',
                'block_robots'    => false,
            ];
            self::$payload->static_routes = [];
        }
        foreach ( $config as $key => $value ) {
            if ( $key == 'user' ) {
                self::$user = $value;
                continue;
            }
            if ( !isset( self::$payload->$key ) ) {
                self::$payload->$key = $value;
            } else {
                self::$payload->$key = array_merge( self::$payload->$key, $value );
            }
        }
    }


    /**
     * Dev tool - show all the data saved in the `Config::$payload` object.
     * 
     * @param   string|null $key    Limit the data shown to only data indexed by this key.
     *                              Default: null.
     * 
     * @static
     * @access  public
     * @since   0.6.0-beta
     */

    public static function show( ?string $key = null ): void {
        echo "<pre>";
        if ( !is_null( $key ) ) {
            print_r( self::$payload->$key ?? 'Key not found.' );
        } else {
            print_r( self::$payload );
        }
        print_r( self::$user );
        echo "</pre>";
    }


    /**
     * Dev tpp; = show all the keys in the `Config::$payload` object.
     * 
     * @static
     * @access  public
     * @since   0.6.0-beta
     */

    public static function keys(): void {
        $keys = array_keys( get_object_vars( self::$payload ) );
        echo "<pre>";
        foreach ( $keys as $key ) {
            print_r( $key ."\n" );
        }
        print_r( '$user' ."\n" );
        echo "</pre>";
    }

}