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
     * Indexes an array by a common key or property of a sub array, or sub object.
     * 
     * @param   string  $index          The index key, who's value to use on the resulting array.
     * @param   array   $array          The array to reindex.
     * @param   bool    $remove_value   Whether or not to remove the origonal key => value or 
     *                                  property from each subarray or subobject.
     * 
     * @return  array
     * 
     * @throws  IndexNotInArray
     * @throws  PropertyNotInObject
     * @throws  ScalarVariable
     */

    public static function index_by(string $index, array $array, bool $remove_value = false): array {
        $new_array = [];
        foreach ($array as $subvalue) {
            if (is_array($subvalue)) {
                if (!isset($subvalue[$index])) {
                    throw new IndexNotInArray("'{$index}' not in a subarray of your array");
                }
                $new_array[$subvalue[$index]] = $subvalue;
                if ($remove_value) {
                    unset($new_array[$subvalue[$index]][$index]);
                }
            } else if (is_object($subvalue)) {
                if (!isset($subvalue->$index)) {
                    throw new PropertyNotInObject("'{$index}' not in a subobject of your array");
                }
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
}
