<?php

namespace LBF\Tools\Array;

use LBF\Errors\Array\IndexNotInArray;
use LBF\Errors\Array\PropertyNotInObject;
use LBF\Errors\Array\ScalarVariable;

/**
 * Verious array helper tools.
 * 
 * use LBF\Tools\Array\ArrayTools;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.7.0-beta
 */

class ArrayTool {

    /**
     * Checks if a defined index is in an array, throwing an exception if not.
     * 
     * @param   array       $array  The array to check.
     * @param   string|int  $index  The index to look for.
     * 
     * @throws  IndexNotInArray
     * 
     * @static
     * @access  private
     * @since   LBF 0.7.0-beta
     */

    private static function check_index_in_array(array $array, string|int $index): void {
        if (!isset($array[$index])) {
            throw new IndexNotInArray("'{$index}' not in a subarray of your array");
        }
    }


    /**
     * Checks if a defined property is in an object, throwing an exception if not.
     * 
     * @param   object  $object     The object to check.
     * @param   string  $property   The property of the object to look for.
     * 
     * @throws  PropertyNotInObject
     * 
     * @static
     * @access  private
     * @since   LBF 0.7.0-beta
     */

    private static function check_property_in_object(object $object, string $property): void {
        if (!isset($object->$property)) {
            throw new PropertyNotInObject("'{$property}' not in a property of the object in your array");
        }
    }


    /**
     * Detects if the array is an associative array or not.
     * 
     * @param   array   $array  The array to test.
     * 
     * @return  bool
     * 
     * @static
     * @access  private
     * @since   LBF 0.7.0-beta
     */

    public static function is_associative(array $array): bool {
        return array_keys($array) === range(0, count($array) - 1);
    }


    /**
     * Indexes an array by a common key or property of a sub array, or sub object.
     * 
     * @param   string  $index          The index key, who's value to use on the resulting array.
     * @param   array   $array          The array to reindex.
     * @param   bool    $remove_value   Whether or not to remove the origonal key => value or 
     *                                  property from each subarray or subobject.
     * 
     * @return  array
     * 
     * @throws  IndexNotInArray Via `check_index_in_array()`
     * @throws  PropertyNotInObject Via `check_property_in_object()`
     * @throws  ScalarVariable
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function index_by(string $index, array $array, bool $remove_value = false): array {
        $new_array = [];
        foreach ($array as $subvalue) {
            if (is_array($subvalue)) {
                self::check_index_in_array($subvalue, $index);
                $new_array[$subvalue[$index]] = $subvalue;
                if ($remove_value) {
                    unset($new_array[$subvalue[$index]][$index]);
                }
            } else if (is_object($subvalue)) {
                self::check_property_in_object($subvalue, $index);
                $new_array[$subvalue->$index] = $subvalue;
                if ($remove_value) {
                    unset($new_array[$subvalue->$index]->$index);
                }
            } else {
                throw new ScalarVariable("The subvalue is scalar, and does not have a property, field or index");
            }
        }
        return $new_array;
    }


    /**
     * Map a common set of key => values from a more complex set of subarrays or subobjects.
     * 
     * @param   array       $array      The array of data to work with.
     * @param   string|int  $new_key    The subkey which will act as the "key" on the new array.
     * @param   string|int  $new_value  The subkey which will act as the "value" on the new array.
     * 
     * @return  array
     * 
     * @throws  IndexNotInArray Via `check_index_in_array()`
     * @throws  PropertyNotInObject Via `check_property_in_object()`
     * @throws  ScalarVariable
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function map(array $array, string|int $new_key, string|int $new_value): array {
        $new_array = [];
        foreach ($array as $sub) {
            if (is_array($sub)) {
                self::check_index_in_array($sub, $new_key);
                self::check_index_in_array($sub, $new_value);
                $new_array[$sub[$new_key]] = $sub[$new_value];
            } else if (is_object($sub)) {
                self::check_property_in_object($sub, $new_key);
                self::check_property_in_object($sub, $new_value);
                $new_array[$sub->$new_key] = $sub->$new_value;
            } else {
                throw new ScalarVariable("The subvalue is scalar, and does not have a property, field or index");
            }
        }
        return $new_array;
    }


    /**
     * Returns a simple array of the values of the parsed key to each subarray or subobject.
     * 
     * @param   array       $array  The array of data to work with.
     * @param   string|int  $key    The key to look for within each subarray or subobject.
     * 
     * @return  array
     * 
     * @throws  IndexNotInArray Via `check_index_in_array()`
     * @throws  PropertyNotInObject Via `check_property_in_object()`
     * @throws  ScalarVariable
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function column(array $array, string|int $key): array {
        return array_map(function ($sub) use ($key) {
            if (is_array($sub)) {
                self::check_index_in_array($sub, $key);
                return $sub[$key];
            } else if (is_object($sub)) {
                self::check_property_in_object($sub, $key);
                return $sub->$key;
            } else {
                throw new ScalarVariable("The subvalue is scalar, and does not have a property, field or index");
            }
        }, $array);
    }


    /**
     * Returns the sum of all the numberic numbers within a simple array.
     * 
     * Note: `true` and `false` are filtered out, which would otherwise be counted
     *       as `1` & `0` respectively.
     * 
     * All non numberic values are simply ignored.
     * 
     * @param   array   $array  The array to add up.
     * 
     * @return  int|float
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function add(array $array): int|float {
        // filters out true, false and other non numeric data types.
        $numeric = array_filter($array, 'is_numeric');
        return array_sum($numeric);
    }


    /**
     * Returns the average of all the numberic numbers within a simple array.
     * 
     * Note: `true` and `false` are filtered out, which would otherwise be counted
     *       as `1` & `0` respectively.
     * 
     * All non numberic values are simply ignored.
     * 
     * @param   array   $array  The array to average up.
     * 
     * @return  int|float
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */


    public static function average(array $array): int|float {
        // filters out true, false and other non numeric data types.
        $numeric = array_filter($array, 'is_numeric');
        $count = count($numeric);
        $total = array_sum($numeric);
        // Avoid DivideByZero Error.
        if ($count == 0) {
            return 0;
        }
        return $total / $count;
    }


    /**
     * Fix the maximum value in a simple array of numbers.
     * 
     * Note: `true` and `false` are filtered out, which would otherwise be counted
     *       as `1` & `0` respectively.
     * 
     * All non numberic values are simply ignored.
     * 
     * @param   array   $array  The array to average up.
     * 
     * @return  int|float
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function max(array $array): int|float {
        // filters out true, false and other non numeric data types.
        $numeric = array_filter($array, 'is_numeric');
        return max($numeric);
    }


    /**
     * Fix the minimum value in a simple array of numbers.
     * 
     * Note: `true` and `false` are filtered out, which would otherwise be counted
     *       as `1` & `0` respectively.
     * 
     * All non numberic values are simply ignored.
     * 
     * @param   array   $array  The array to average up.
     * 
     * @return  int|float
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function min(array $array): int|float {
        // filters out true, false and other non numeric data types.
        $numeric = array_filter($array, 'is_numeric');
        return min($numeric);
    }


    /**
     * Removes a value from the array (by key) as returns it. The rest of the array is preserved.
     * 
     * @param   mixed   $key    The key to remove.
     * @param   array   &$array The array to search from.
     * 
     * @return  mixed   Whatever the value of the key is.
     * 
     * @throws  IndexNotInArray
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function remove(mixed $key, array &$array): mixed {
        if (!isset($array[$key])) {
            throw new IndexNotInArray("Can't remove key that is not in the array.");
        }
        $value = $array[$key];
        unset($array[$key]);
        return $value;
    }


    /**
     * Removes the maximum integer or float value from an array. If the array is associative
     * You have the option to reindex the values, otherwise the previously non associative
     * becomes associative.
     * 
     * Reindexing is the default behaviour.
     * 
     * @param   array   &$array     The array to operate on.
     * @param   bool    $reindex    Whether or not to reindex the array. Default: true.
     *                              Will do nothing if the array is an associative array.
     * 
     * @return  int|float
     * 
     * @throws  ValueError  See `self::max`
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function remove_max(array &$array, bool $reindex = true): int|float {
        if (self::is_associative($array) && $reindex) {
            $remove = self::remove(array_search(self::max($array), $array, true), $array);
            $array = array_values($array);
            return $remove;
        }
        return self::remove(array_search(self::max($array), $array, true), $array);
    }


    /**
     * Removes the minimum integer or float value from an array. If the array is associative
     * You have the option to reindex the values, otherwise the previously non associative
     * becomes associative.
     * 
     * Reindexing is the default behaviour.
     * 
     * @param   array   &$array     The array to operate on.
     * @param   bool    $reindex    Whether or not to reindex the array. Default: true.
     *                              Will do nothing if the array is an associative array.
     * 
     * @return  int|float
     * 
     * @throws  ValueError  See `self::max`
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function remove_min(array &$array, bool $reindex = true): int|float {
        if (self::is_associative($array) && $reindex) {
            $remove = self::remove(array_search(self::min($array), $array, true), $array);
            $array = array_values($array);
            return $remove;
        }
        return self::remove(array_search(self::min($array), $array, true), $array);
    }


    /**
     * Test if a key or especially multiple keys exist within an array.
     * 
     * @param   array   $array  The array to examine.
     * @param   array   $array  An array of keys to test against.
     * 
     * @return  bool
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.5-beta
     */

    public static function keys_exists(array $array, array $keys): bool {
        $diff = array_diff_key(array_flip($keys), $array);
        return count($diff) === 0;
    }
}
