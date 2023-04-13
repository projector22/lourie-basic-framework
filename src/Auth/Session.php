<?php

namespace LBF\Auth;

/**
 * This class handles a number of tasks related to sessions and the $_SESSION variable.
 * 
 * use LBF\Auth\Session;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.15.11
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class Session {

    /**
     * Start a new session.
     * 
     * @param   boolean $hide_session_start_info    Default: true
     * 
     * @access  public
     * @since   LRS 3.15.11
     * @since   LBF 0.2.2-beta  Removed param `$hide_session_start_info`.
     */

    public static function start(): void {
        if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }


    /**
     * Create a session variable.
     * 
     * @param   string  $id     The ID of the $_SESSION variable to create.
     * @param   mixed   $value  The data to assign to $_SESSION variable.
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function set_value(string $id, mixed $value): void {
        $_SESSION[$id] = $value;
    }


    /**
     * Destroy a session variable.
     * 
     * @param   string  $id             The ID of the $_SESSION variable to be destroyed.
     * @param   boolean $strick_check   Whether or not to check if the $_SESSION variable preexists
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function destroy_value(string $id, bool $strict_check = false): void {
        if ($strict_check) {
            if (!isset($_SESSION[$id])) {
                throw new \Exception("\$_SESSION['{$id}'] does not exist.");
            }
        }
        unset($_SESSION[$id]);
    }


    /**
     * Destroy all session variables that exists.
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function destroy_all_values(): void {
        if (!self::has_started()) {
            return;
        }
        foreach ($_SESSION as $id => $session) {
            self::destroy_value($id);
        }
    }


    /**
     * Check if sessions have started.
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function has_started(): bool {
        if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
            return false;
        }
        return true;
    }


    /**
     * Check if a $_SESSSION variable exists.
     * 
     * @param   string  $id     ID of the string variable
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function value_exists(string $id): bool {
        return isset($_SESSION[$id]);
    }


    /**
     * Get the $_SESSION[$id] set value. If the value is an array or object, a value or property
     * may be pulled out by parsing the `$index` argument.
     * 
     * @param   string  $id     ID of the string variable
     * @param   string  $index  The parsed arguments as a property or index. 
     *                          Ignored if the value / property doesn't exist.
     * 
     * @return  mixed
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function value(string $id, ?string $index = null): mixed {
        if (!isset($_SESSION[$id])) {
            return null;
        }
        if (!is_null($index)) {
            if (is_array($_SESSION[$id])) {
                if (isset($_SESSION[$id][$index])) {
                    return $_SESSION[$id][$index];
                }
            } else if (is_object($_SESSION[$id])) {
                if (isset($_SESSION[$id]->$index)) {
                    return $_SESSION[$id]->$index;
                }
            }
        }
        return $_SESSION[$id];
    }


    /**
     * Magic method to call $_SESSION value as a static 
     * method.
     * 
     * If the session variable is an array or object, a specific
     * value can be called as an argument to the magic method
     * 
     * @param   string  $id         ID of the string variable
     * @param   array   $arguments  The parsed arguments to the magic method
     * 
     * @return  mixed
     * 
     * @access  public
     * @since   LRS 3.15.11
     */

    public static function __callStatic(string $id, array $arguments): mixed {
        if (is_array($_SESSION[$id])) {
            if (isset($arguments[0]) && isset($_SESSION[$id][$arguments[0]])) {
                return $_SESSION[$id][$arguments[0]];
            }
        } else if (is_object($_SESSION[$id])) {
            if (isset($arguments[0]) && isset($_SESSION[$id]->$arguments[0])) {
                return $_SESSION[$id]->$arguments[0];
            }
        }
        return $_SESSION[$id];
    }
}
