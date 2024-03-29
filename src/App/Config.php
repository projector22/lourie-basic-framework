<?php

namespace LBF\App;

use AllowDynamicProperties;
use Exception;

/**
 * Class for holding the payload of config files to be used by the app.
 * Load in a config array and it should be available via `Config::my_key()` throughout the app.
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
     * The default meta details of the current application.
     * 
     * @var array   META_DEFAULT
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

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

    /**
     * The default static routes defined in the current application.
     * 
     * @var array   STATIC_ROUTES_DEFAULT
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private const STATIC_ROUTES_DEFAULT = [
        /**
         * http
         */
        '/home'    => 'Web\IndexPage',
        '/index'   => 'Web\IndexPage',
        '/login'   => 'Web\LoginPage',
        '/logout'  => 'Web\LogoutPage',

        /**
         * http wildcard
         */
        '/pdf/*'       => 'PDF\PDFHandler',
        '/download/*'  => 'Downloads\DownloadHandler',

        /**
         * API
         */
        '/actions' => '\Actions\ActionHander',
    ];


    /**
     * Load in the default data to the Config object.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function load_defaults(): void {
        self::$payload = [
            'meta' => self::META_DEFAULT,
            'user' => null,
            'static_routes' => self::STATIC_ROUTES_DEFAULT
        ];
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

    public static function cast_as_object(array|object $data): object {
        if (is_array($data)) {
            return (object)$data;
        }
        return $data;
    }


    /**
     * The general idea here is to take an already existing value in the config and modify it.
     * 
     * ### Example
     * 
     * ```php
     * Config::env('cheese') == 'Cake'; // true
     * Config::set_value('env', 'cheese', 'mouse');
     * Config::env('cheese') == 'Cake'; // false
     * echo Config::env('cheese'); // 'mouse'
     * ```
     * 
     * @param   mixed       $new_value  The new value to inject into the Config.
     * @param   string      $method     The already defined method in the config.
     * @param   string|null $key        The subvalue of the $method. May be parsed as null if the $method is simple data (an int, string bool etc).
     * 
     * @return  bool
     * 
     * @throws  Exception
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function set_value(mixed $new_value, string $method, ?string $key = null): bool {
        if (!isset(self::$payload[$method])) {
            throw new Exception("Key {$method} is not in the config.");
        }
        if ($key === null) {
            self::$payload[$method] = $new_value;
            return true;
        } else {
            self::$payload[$method][$key] = $new_value;
            return true;
        }
    }


    /**
     * Check if a key exists within the config. In other words, will Config::$key() give a result.
     * 
     * @param   string  $key    The key to search for
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function key_exists(string $key): bool {
        return array_key_exists($key, self::$payload) || property_exists(self::class, $key);
    }


    /**
     * Magic method for retrieving data from the Config object. This allows for the following example:
     * 
     * ```php
     * $data = ['cheese' => ['mouse' => ['trap']]];
     * Config::load( $data );
     * echo Config::cheese('mouse');
     * ```
     * 
     * Note, if argument 1 is parsed, it should be a bool, and if true, the method will throw an exception rather
     * than return null. Useful for debugging.
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

    public static function __callStatic(string $name, array $arguments): mixed {
        if (!self::key_exists($name)) {
            if (isset($arguments[1]) && $arguments[1] === true) {
                throw new Exception("The method {$name} is not part of the Config.");
            }
            return null;
        }
        if (count($arguments) == 0) {
            return self::$$name ?? self::$payload[$name];
        }
        $method = self::$payload[$name];
        if (is_object($method)) {
            if (!isset($method->{$arguments[0]}) && !property_exists($method, $arguments[0])) {
                if (isset($arguments[1]) && $arguments[1] === true) {
                    throw new Exception("The key {$arguments[0]} is not in the method {$name}");
                }
                return null;
            }
            return $method->{$arguments[0]};
        }
        if (!isset($method[$arguments[0]]) && !array_key_exists($arguments[0], $method)) {
            if (isset($arguments[1]) && $arguments[1] === true) {
                throw new Exception("The key {$arguments[0]} is not in the method {$name}");
            }
            return null;
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

    public static function load(array $config, bool $overwrite = false): void {
        if (!isset(self::$payload)) {
            self::load_defaults();
        }

        foreach ($config as $key => $value) {
            if (isset(self::$$key)) {
                if ($overwrite) {
                    self::$$key = $value;
                } else {
                    if (is_array(self::$$key)) {
                        // Merge arrays if that is being parsed
                        if (!is_array($config)) {
                            throw new Exception("{$value} must be an array");
                        }
                        self::$$key = array_merge(self::$$key, $value);
                    } else if (is_object(self::$$key)) {
                        // Merge objects if that is being parsed.
                        if (!is_object($value)) {
                            throw new Exception("{$value} must be a set of key => value pairs");
                        }
                        foreach ($value as $prop => $val) {
                            self::$$key->{$prop} = $val;
                        }
                    } else {
                        // Overwrite if the data isn't an array or object
                        self::$$key = $value;
                    }
                }
            } else {
                if ($overwrite || !isset(self::$payload[$key])) {
                    self::$payload[$key] = $value;
                } else {
                    if (is_array(self::$payload[$key])) {
                        // Merge arrays if that is being parsed
                        if (!is_array($config)) {
                            throw new Exception("{$value} must be an array");
                        }
                        self::$payload[$key] = array_merge(self::$payload[$key], $value);
                    } else if (is_object(self::$payload[$key])) {
                        // Merge objects if that is being parsed.
                        if (!is_array($value)) {
                            throw new Exception("{$value} must be a set of key => value pairs");
                        }
                        foreach ($value as $prop => $val) {
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

    public static function show(?string $specified_key = null): void {
        if (!is_null($specified_key)) {
            echo "<h3>{$specified_key}</h3>";
            echo "<pre>";
            print_r(self::$$specified_key ?? self::$payload[$specified_key]);
            echo "</pre>";
        } else {
            $keys = self::get_class_keys();
            echo "<pre>";
            foreach ($keys as $key) {
                if (isset(self::$$key) || isset(self::$payload[$key])) {
                    echo "<h3>{$key}</h3>";
                    print_r(self::$$key ?? self::$payload[$key]);
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
        foreach ($keys as $key) {
            print_r($key . "\n");
        }
        print_r('$user' . "\n");
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
        $set_properties = get_class_vars(self::class);
        unset($set_properties['payload']);
        $keys = array_merge(array_keys(self::$payload), array_keys($set_properties));
        sort($keys);
        return $keys;
    }
}
