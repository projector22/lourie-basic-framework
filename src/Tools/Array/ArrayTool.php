<?php

namespace LBF\Tools\Array;

/**
 * @todo
 * 
 * - [x] index_by   set a common index as parent index.
 * - [x] map        map two common values within each subarray or object properties as key => value pairs
 * - [x] column     Get the values of a common key as a simple array.
 * - [ ] add        Add all the values of an array for a total.
 * - [ ] average    Get the average of the values for an array.
 */

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
}
