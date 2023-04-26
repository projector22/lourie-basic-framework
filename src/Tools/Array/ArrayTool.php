<?php

namespace LBF\Tools\Array;

use LBF\Errors\Array\IndexNotInArray;

class ArrayTool {

    /**
     * @throws  IndexNotInArray
     */

    public static function index_by(string $index, array $array, bool $remove_value = false): array {
        $new_array = [];
        foreach ($array as $subarray) {
            if (!isset($subarray[$index])) {
                throw new IndexNotInArray("'{$index}' not in a subarray of your array");
            }
            if ($remove_value) {
                unset($subarray[$index]);
            }
            $new_array[$subarray[$index]] = $subarray;
        }
    }
}