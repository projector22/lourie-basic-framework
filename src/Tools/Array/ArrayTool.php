<?php

namespace LBF\Tools\Array;

use LBF\Errors\Array\IndexNotInArray;
use LBF\Errors\Array\PropertyNotInObject;
use LBF\Errors\Array\ScalarVariable;

class ArrayTool {

    /**
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
                    unset($subvalue[$index]);
                }
            } else if (is_object($subvalue)) {
                if (!isset($subvalue->$index)) {
                    throw new PropertyNotInObject("'{$index}' not in a subobject of your array");
                }
                $new_array[$subvalue->$index] = $subvalue;
                if ($remove_value) {
                    unset($subvalue->$index);
                }
            } else {
                throw new ScalarVariable("The subvalue is scalar, and does not have a property, field or index");
            }
        }
        return $new_array;
    }
}
