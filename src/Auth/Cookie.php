<?php

namespace LBF\Auth;

use Exception;

/**
 * This class handles the creation, update and deletion cookies and the variable $_COOKIE.
 * 
 * use LBF\Auth\Cookie;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.28.0
 * @since   LBF 0.1.2-beta
 */

class Cookie {

    /**
     * Contains the default duration of any cookie that doesn't have a time explicity set.
     * 
     * @var int $duration
     * 
     * @static
     * @access  private
     * @since   0.1.2-beta
     */

    private static int $duration;

    /**
     * Set a cookie value.
     * 
     * @param string                $name  The name of the cookie.
     * @param string|array|object   $value
     * [optional]
     * 
     * The value of the cookie. This value is stored on the clients computer; do not store sensitive information. 
     * Assuming the name is 'cookiename', this value is retrieved through $_COOKIE['cookiename']
     * 
     * @param int $expires
     * [optional]
     * 
     * The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch. In other words, 
     * you'll most likely set this with the time function plus the number of seconds before you want it to expire. Or you 
     * might use mktime. `time()+60*60*24*30` will set the cookie to expire in 30 days. If set to 0, or omitted, the cookie 
     * will expire at the end of the session (when the browser closes).
     * 
     * You may notice the expire parameter takes on a Unix timestamp, as opposed to the date format Wdy, DD-Mon-YYYY HH:MM:SS GMT, 
     * this is because PHP does this conversion internally.
     * 
     * expire is compared to the client's time which can differ from server's time.
     * 
     * @param string $path
     * [optional]
     * 
     * The path on the server in which the cookie will be available on. If set to '/', the cookie will be available within the entire 
     * domain. If set to '/foo/', the cookie will only be available within the /foo/ directory and all sub-directories such as /foo/bar/ 
     * of domain. The default value is the current directory that the cookie is being set in.
     * 
     * @param string $domain
     * [optional]
     * 
     * The domain that the cookie is available. To make the cookie available on all subdomains of example.com then you'd set it to 
     * '.example.com'. The . is not required but makes it compatible with more browsers. Setting it to www.example.com will make the
     * cookie only available in the www subdomain. Refer to tail matching in the spec for details.
     * 
     * @param bool $secure
     * [optional]
     * 
     * Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client. When set to true, the cookie 
     * will only be set if a secure connection exists. On the server-side, it's on the programmer to send this kind of cookie only on secure 
     * connection (e.g. with respect to $_SERVER["HTTPS"]).
     * 
     * @param bool $httponly
     * [optional]
     * 
     * When true the cookie will be made accessible only through the HTTP protocol. This means that the cookie won't be accessible by scripting
     * languages, such as JavaScript. This setting can effectively help to reduce identity theft through XSS attacks (although it is not 
     * supported by all browsers). Added in PHP 5.2.0. true or false
     * 
     * @return bool
     * If output exists prior to calling this function, setcookie will fail and return false. If setcookie successfully runs, it will return true. 
     * This does not indicate whether the user accepted the cookie.
     * 
     * @link https://php.net/manual/en/function.setcookie.php
     * 
     * @return  bool
     * 
     * @throws  Exception   If the page's headers are already sent or if the cookie size is too big.
     * 
     * @static
     * @access  public
     * @since   0.1.2-beta
     */

    public static function set_value(
        string $name,
        string|array|object $value = "",
        ?int $expires = null,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false 
    ): bool {
        if ( headers_sent() ) {
            throw new Exception( "Cannot set cookie, Headers already sent." );
        }
        if ( is_null( $expires ) ) {
            if ( !isset( self::$duration ) ) {
                self::set_default_duration( 60*60*24*30 );
            }
            $expires = self::$duration;
        }
        if ( is_array( $value ) || is_object( $value ) ) {
            $value = json_encode( $value );
        }
        $value = self::encode_cookie( $value );
        if ( strlen( $value ) > 4000 ) {
            /**
             * @todo    In the future, it would be nice to be able to split this into multiple parts
             *          if too big and handle this automatically.
             * 
             * @see     https://www.php.net/manual/en/function.setcookie.php (Comment: Eric)
             * 
             * @since   0.1.2-beta
             */
            throw new Exception( "Cookie size bigger than 4k limit. Size is " . strlen( $value ) );
        }
        return setcookie( 
            $name, 
            $value,
            $expires,
            $path, 
            $domain, 
            $secure, 
            $httponly
        );
    }


    /**
     * get a value string, array or object from the existing cookies.
     * 
     * @param   string  $name               The key of cookie to retrieve.
     * @param   boolean $decode_to_object   If the cookie value is a JSON string, it can be decoded to an 
     *                                      object by setting this to true.
     *                                      Default: false.
     * @param   boolean $decode_to_array    If the cookie value is a JSON string, it can be decoded to an
     *                                      array by setting this to true. If `$decode_to_object` is also
     *                                      parsed as true, this param is ignored.
     * 
     * @return  string|array|object
     * 
     * @throws  Exception   If the key isn't in $_COOKIE.
     * 
     * @static
     * @access  public
     * @since   0.1.2-beta
     */

    public static function get_value( string $name, bool $decode_to_object = false, bool $decode_to_array = false ): string|array|object {
        if ( !self::value_exists( $name ) ) {
            throw new Exception( "Key '{$name}' not a cookie." );
        }
        $data = self::decode_cookie( $_COOKIE[$name] );
        if ( $decode_to_object ) {
            return json_decode( $data );
        } else if ( $decode_to_array ) {
            return json_decode( $data, true );
        }
        return $data;
    }


    /**
     * Unset a specified cookie on the site.
     * 
     * @param   string  $name   The key of the Cookie being destroyed.
     * 
     * @static
     * @access  public
     * @since   0.1.2-beta
     */

    public static function destroy_value( string $name ): void {
        setcookie( $name, '', 1 );
        unset( $_COOKIE[$name] );
    }


    /**
     * Unset all cookies on the site.
     * 
     * @static
     * @access  public
     * @since   0.1.2-beta
     */

    public static function destroy_all_values(): void {
        foreach( $_COOKIE as $name => $value ) {
            self::destroy_value( $name );
        }
    }


    /**
     * Check if a cookie has been set and is in `$_COOKIE`.
     * 
     * @param   string  $name   The cookie key.
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   0.1.2-beta
     */

    public static function value_exists( string $name ): bool {
        return isset( $_COOKIE[$name] );
    }


    /**
     * Magic method to call a cookie name directly using the `Cookie` class.
     * 
     * @example Cookie::my_cookie();
     * 
     * @param   string  $id         The name of the cookie name to be called in this method.
     * @param   array   $arguments  Any arguments to be parsed. `array` or `object` can be
     *                              parsed to return a JSON decoded array or object. Any 
     *                              other string parsed will function as an array key, returning
     *                              just that value from a JSON decoded array if desired.
     * 
     * @return  mixed
     * 
     * @throws  Exception   If the key does not exist in the JSON decoded cookie value.
     * 
     * @static
     * @access  public
     * @since   0.1.2-beta
     */

    public static function __callStatic( string $id, array $arguments ): mixed {
        $data = self::decode_cookie( $_COOKIE[$id] );
        $args = array_flip( $arguments );
        if ( isset( $args['object'] ) ) {
            return json_decode( $data );
        } else if ( isset( $args['array'] ) ) {
            return json_decode( $data, true );
        } else {
            $hold_data = json_decode( $data, true );
            if ( count( $hold_data ) > 0 ) {
                if ( !isset( $hold_data[$arguments[0]] ) ) {
                    throw new Exception( "Key '{$arguments[0]}' is not in Cookie '{$id}'" );
                }
                return $hold_data[$arguments[0]];
            }
        }
        return $data;
    }


    /**
     * Encode and compress the value of the cookie so that it is not easily human readable.
     * 
     * @param   string  $value  The value to be encoded.
     * 
     * @return  string
     * 
     * @static
     * @access  private
     * @since   0.1.2-beta
     */

    private static function encode_cookie( string $value ): string {
        $value = serialize( $value );
        $value = gzcompress( $value );
        $value = base64_encode( $value );
        return $value;
    }


    /**
     * Decode and compress the value of the cookie so that it is again easily human readable.
     * 
     * @param   string  $value  The value to be decoded.
     * 
     * @return  string
     * 
     * @static
     * @access  private
     * @since   0.1.2-beta
     */

    private static function decode_cookie( string $value ): string {
        $value = base64_decode( $value );
        $value = gzuncompress( $value );
        $value = unserialize( $value );
        return $value;
    }


    /**
     * Set the default duration of all cookies being set by this class.
     * 
     * @param   int|string  $duration   The duration can either be sent as a UNIX timestamp or as a string
     *                                  time stamp something like '+30 days'.
     * 
     * ### Example Strings for `$duration`
     * 
     * - `now`
     * - `10 September 2020`
     * - `+1 day`
     * - `+1 week`
     * - `+1 week 2 days 4 hours 2 seconds`
     * - `next Thursday`
     * - `last Monday`
     * 
     * @param   bool    $include_time   If parsing `$duration` as a int, you would normally parse a value
     *                                  like `time()+1234`. This param adds the time() for you if parsed
     *                                  `true`.
     *                                  Default: `true`
     * 
     * @see https://www.php.net/manual/en/function.strtotime.php
     * 
     * @return  bool
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.2-beta
     */

    public static function set_default_duration( int|string $duration, bool $include_time = true ): bool {
        if ( is_string( $duration ) ) {
            return self::$duration = strtotime( $duration );
        }
        if ( $include_time ) {
            return self::$duration = time() + $duration;
        }
        return self::$duration = $duration;        
    }

}
