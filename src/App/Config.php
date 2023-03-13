<?php

namespace LBF\App;

use AllowDynamicProperties;
use Exception;
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
     * @var array    $payload
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static array $payload;

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

    private const META_DEFAULT = [
        'app_name'        => 'YOUR APP NAME',
        'description'     => 'A basic PHP Framework',
        'project_version' => '0.1.0',
        'project_status'  => '',
        'page_title'      => 'Lourie Basic Framework',
        'favicon'         => '',
        'site_language'   => 'en',
        'block_robots'    => false,
    ];

    private const STATIC_ROUTES_DEFAULT = [];

    public static array $meta;
    public static array $static_routes;


    /**
     * Load in the default data to the Config object.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function load_defaults(): void {
        self::$meta = self::META_DEFAULT;
        self::$static_routes = self::STATIC_ROUTES_DEFAULT;
    }


    /**
     * Converts the parsed data to an object if an array.
     * 
     * @param   array|object    $data   Data to convert to object.
     * 
     * @return  object
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function cast_as_object( array|object $data ): object {
        if ( is_array( $data ) ) {
            return (object)$data;
        }
        return $data;
    }


    /**
     * Magic method for retrieving data from the Config object. This allows for the following example
     * 
     * ```php
     * $data = ['cheese' => ['mouse' => ['trap']]];
     * Config::load( $data );
     * echo Config::cheese('mouse');
     * ```
     * 
     * @param   string  $name
     * @param   array   $arguments
     * 
     * @return  mixed
     * 
     * @throws  Exception
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function __callStatic( string $name, array $arguments ): mixed {
        if ( !isset( self::$payload[$name] ) && !isset( self::$$name ) ) {
            throw new Exception( "The method {$name} is not part of the Config." );
        }
        if ( count( $arguments ) == 0 ) {
            return self::$$name ?? self::$payload[$name];
        }
        $method = self::$$name ?? self::$payload[$name];
        if ( is_object( $method ) ) {
            if ( !isset( $method->{$arguments[0]} ) ) {
                throw new Exception( "The key {$arguments[0]} is not in the method {$name}" );
            }
            return $method->{$arguments[0]};
        }
        if ( !isset( $method[$arguments[0]] ) ) {
            throw new Exception( "The key {$arguments[0]} is not in the method {$name}" );
        }
        return $method[$arguments[0]];
    }



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
        if ( !isset( self::$meta ) || !isset( self:: $static_routes ) ) {
            self::load_defaults();
        }

        foreach ( $config as $key => $value ) {
            if ( isset( self::$$key ) ) {
                if ( $overwrite ) {
                    self::$$key = $value;
                } else {
                    if ( is_array( self::$$key ) ) {
                        // Merge arrays if that is being parsed
                        if ( !is_array( $config ) ) {
                            throw new Exception( "{$value} must be an array" );
                        }
                        self::$$key = array_merge( self::$$key, $value );
                    } else if ( is_object( self::$$key ) ) {
                        // Merge objects if that is being parsed.
                        if ( !is_object( $value ) ) {
                            throw new Exception( "{$value} must be a set of key => value pairs" );
                        }
                        foreach ( $value as $prop => $val ) {
                            self::$$key->{$prop} = $val;
                        }
                    } else {
                        // Overwrite if the data isn't an array or object
                        self::$$key = $value;
                    }
                }
            } else {
                if ( $overwrite || !isset( self::$payload[$key] ) ) {
                    self::$payload[$key] = $value;
                } else {
                    if ( is_array( self::$payload[$key] ) ) {
                        // Merge arrays if that is being parsed
                        if ( !is_array( $config ) ) {
                            throw new Exception( "{$value} must be an array" );
                        }
                        self::$payload[$key] = array_merge( self::$payload[$key], $value );
                    } else if ( is_object( self::$payload[$key] ) ) {
                        // Merge objects if that is being parsed.
                        if ( !is_array( $value ) ) {
                            throw new Exception( "{$value} must be a set of key => value pairs" );
                        }
                        foreach ( $value as $prop => $val ) {
                            self::$payload[$key]->{$prop} = $val;
                        }
                    } else {
                        // Overwrite if the data isn't an array or object
                        self::$payload[$key] = $value;
                    }
                }
            }
        }
    }


    /**
     * Dev tool - show all the data saved in the `Config::$payload` object.
     * 
     * @param   string|null $specified_key  Limit the data shown to only data indexed by this key.
     *                                      Default: null.
     * 
     * @static
     * @access  public
     * @since   0.6.0-beta
     */

    public static function show( ?string $specified_key = null ): void {
        if ( !is_null( $specified_key ) ) {
            print_r( self::$$specified_key ?? self::$payload[$specified_key] );
        } else {
            $keys = self::get_class_keys();
            echo "<pre>";
            foreach ( $keys as $key ) {
                if ( isset( self::$$key ) || isset( self::$payload[$key] ) ) {
                    echo "<h3>{$key}</h3>";
                    print_r( self::$$key ?? self::$payload[$key] );
                    echo "<hr>";
                }
            }
            echo "</pre>";
        }
    }


    /**
     * Dev tpp; = show all the keys in the `Config::$payload` object.
     * 
     * @static
     * @access  public
     * @since   0.6.0-beta
     */

    public static function keys(): void {
        $keys = self::get_class_keys();
        echo "<pre>";
        foreach ( $keys as $key ) {
            print_r( $key ."\n" );
        }
        print_r( '$user' ."\n" );
        echo "</pre>";
    }


    /**
     * Get the keys used by the application config.
     * 
     * @return  array
     * 
     * @static
     * @access  private
     * @since   0.6.0-beta
     */

    private static function get_class_keys(): array {
        $set_properties = get_class_vars( self::class );
        unset( $set_properties['payload'] );
        $keys = array_merge( array_keys( self::$payload ), array_keys( $set_properties ) );
        sort( $keys );
        return $keys;
    }

}